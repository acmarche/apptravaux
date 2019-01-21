<?php

$container->loadFromExtension(
    'framework',
    array(
        'workflows' => array(
            'intervention_publication' => array(
                'type' => 'state_machine',
                'marking_store' => ['type' => 'multiple_state', 'arguments' => 'currentPlace'],
                'supports' => array(\AcMarche\Travaux\Entity\Intervention::class),
                'places' => [
                    'auteur_checking',
                    'redacteur',
                    'admin_checking',
                    'deleted',
                    'published',
                ],
                'transitions' => array(
                    'auteur_accept' => array(
                        'from' => 'auteur_checking',
                        'to' => 'admin_checking',
                    ),
                    'info_back_auteur' => array(
                        'from' => 'admin_checking',
                        'to' => 'auteur_checking',
                    ),
                    'info_back_contributeur' => array(
                        'from' => 'admin_checking',
                        'to' => 'auteur_checking',
                    ),
                    'info_back_redacteur' => array(
                        'from' => 'admin_checking',
                        'to' => 'admin_checking',
                    ),
                    'publish' => array(
                        'from' => 'admin_checking',
                        'to' => 'published',
                    ),
                    'reject' => array(
                        'from' => ['auteur_checking', 'admin_checking'],
                        'to' => 'deleted',
                    ),
                ),
            ),
        ),
    )
);