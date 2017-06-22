<?php

    /*
     * This file is part of the Sonata Project package.
     *
     * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    namespace Application\Sonata\ClassificationBundle\Admin;

    use Sonata\AdminBundle\Form\FormMapper;

    class CategoryAdmin extends \Sonata\ClassificationBundle\Admin\CategoryAdmin
    {
        /**
         * {@inheritdoc}
         */
        protected function configureFormFields(FormMapper $formMapper)
        {
            $formMapper->with('General', ['class' => 'col-md-6'])
                ->add('name')
                ->add('description',
                      'textarea',
                      [
                          'required' => false,
                      ])
            ;

            if ($this->hasSubject()) {
                if ($this->getSubject()
                        ->getParent() !== null || $this->getSubject()
                        ->getId() === null
                ) { // root category cannot have a parent
                    $formMapper->add('parent',
                                     'sonata_category_selector',
                                     [
                                         'category' => $this->getSubject() ?: null,
                                         'model_manager' => $this->getModelManager(),
                                         'class' => $this->getClass(),
                                         'required' => false,
                                         'context' => $this->getSubject()
                                             ->getContext(),
                                         'placeholder' => '',
                                     ]);
                }
            }

            $position = $this->hasSubject() && !is_null($this->getSubject()
                                                            ->getPosition()) ? $this->getSubject()
                ->getPosition() : 0;

            $formMapper->end()
                ->with('Options', ['class' => 'col-md-6'])
                ->add('enabled',
                      null,
                      [
                          'required' => false,
                      ])
                ->add('position',
                      'integer',
                      [
                          'required' => false,
                          'data' => $position,
                      ])
                ->end()
            ;

            if (!array_key_exists('context', $this->getRequest()->query->all())) {
                $formMapper->with('Options')
                    ->add('context',
                          'entity',
                          [
                              'required' => true,
                              'class' => 'Application\Sonata\ClassificationBundle\Entity\Context',
                              'choice_label' => 'name',
                              'placeholder' => false,
                          ])
                    ->end()
                ;
            }

            if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
                $formMapper->with('General')
                    ->add('media',
                          'sonata_type_model_list',
                          [
                              'required' => false,
                          ],
                          [
                              'link_parameters' => [
                                  'provider' => 'sonata.media.provider.image',
                                  'context' => ($this->hasSubject() && $this->getSubject()
                                          ->getContext()) ? $this->getSubject()
                                      ->getContext()
                                      ->getId() : 'default',
                              ],
                          ])
                    ->end()
                ;
            }
        }
    }