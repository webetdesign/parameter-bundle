<?php

declare(strict_types=1);

namespace WebEtDesign\ParameterBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ParameterAdminController extends CRUDController
{

    /**
     * Edit action.
     *
     * @param int|string|null $deprecatedId
     *
     * @return Response|RedirectResponse
     * @throws AccessDeniedException If access is not granted
     *
     * @throws NotFoundHttpException If the object does not exist
     */
    public function editAction($deprecatedId = null) // NEXT_MAJOR: Remove the unused $id parameter
    {
        if (isset(\func_get_args()[0])) {
            @trigger_error(
                sprintf(
                    'Support for the "id" route param as argument 1 at `%s()` is deprecated since'
                    .' sonata-project/admin-bundle 3.62 and will be removed in 4.0,'
                    .' use `AdminInterface::getIdParameter()` instead.',
                    __METHOD__
                ),
                \E_USER_DEPRECATED
            );
        }

        // the key used to lookup the template
        $templateKey = 'edit';

        $request        = $this->getRequest();
        $id             = $request->get($this->admin->getIdParameter());
        $existingObject = $this->admin->getObject($id);

        if (!$existingObject) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        if ($existingObject->getType() === 'file' && $existingObject->getValue()) {
            try {
                $existingObject->setFile(
                    new File(
                        $this->getParameter($existingObject->getCode().'_directory').'/'.$existingObject->getValue()
                    )
                );
            } catch (FileNotFoundException $e) {
                $existingObject->setValue(null);
            }
        }

        $this->checkParentChildAssociation($request, $existingObject);

        $this->admin->checkAccess('edit', $existingObject);

        $preResponse = $this->preEdit($request, $existingObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($existingObject);
        $objectId = $this->admin->getNormalizedIdentifier($existingObject);

        $form = $this->admin->getForm();

        $form->setData($existingObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                /** @phpstan-var T $submittedObject */
                $submittedObject = $form->getData();

                if ($submittedObject->getType() === 'file') {
                    $file = $form->get('file')->getData();

                    if ($file) {
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = transliterator_transliterate(
                            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                            $originalFilename
                        );
                        $newFilename  = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                        // Move the file to the directory
                        try {
                            $file->move(
                                $this->getParameter($submittedObject->getCode().'_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // clean old file
                        try {
                            $this->get('filesystem')->remove(
                                $this->getParameter(
                                    $submittedObject->getCode().'_directory'
                                ).DIRECTORY_SEPARATOR.$submittedObject->getValue()
                            );
                        } catch (IOException $e) {

                        }

                        $submittedObject->setValue($newFilename);
                    }
                }

                $this->admin->setSubject($submittedObject);

                try {
                    $existingObject = $this->admin->update($submittedObject);

                    if ($this->isXmlHttpRequest()) {
                        return $this->handleXmlHttpRequestSuccessResponse($request, $existingObject);
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_edit_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($existingObject);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_lock_error',
                            [
                                '%name%'       => $this->escapeHtml($this->admin->toString($existingObject)),
                                '%link_start%' => sprintf(
                                    '<a href="%s">',
                                    $this->admin->generateObjectUrl('edit', $existingObject)
                                ),
                                '%link_end%'   => '</a>',
                            ],
                            'SonataAdminBundle'
                        )
                    );
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if ($this->isXmlHttpRequest() && null !== ($response = $this->handleXmlHttpRequestErrorResponse(
                        $request,
                        $form
                    ))) {
                    return $response;
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_edit_error',
                        ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                        'SonataAdminBundle'
                    )
                );
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFormTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate($templateKey);

        // $template = $this->templateRegistry->getTemplate($templateKey);

        return $this->renderWithExtraParams(
            $template,
            [
                'action'   => 'edit',
                'form'     => $formView,
                'object'   => $existingObject,
                'objectId' => $objectId,
            ],
            null
        );
    }

    /**
     * @phpstan-param T $object
     */
    private function checkParentChildAssociation(Request $request, object $object): void
    {
        if (!$this->admin->isChild()) {
            return;
        }

        // NEXT_MAJOR: remove this check
        if (!$this->admin->getParentAssociationMapping()) {
            return;
        }

        $parentAdmin = $this->admin->getParent();
        $parentId    = $request->get($parentAdmin->getIdParameter());

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyPath     = new PropertyPath($this->admin->getParentAssociationMapping());

        if ($parentAdmin->getObject($parentId) !== $propertyAccessor->getValue($object, $propertyPath)) {
            // NEXT_MAJOR: make this exception
            @trigger_error(
                'Accessing a child that isn\'t connected to a given parent is deprecated since sonata-project/admin-bundle 3.34 and won\'t be allowed in 4.0.',
                \E_USER_DEPRECATED
            );
        }
    }

    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     */
    private function setFormTheme(FormView $formView, ?array $theme = null): void
    {
        $twig = $this->get('twig');

        $twig->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
    }
}