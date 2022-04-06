<?php

return [
    '__name' => 'admin-product-brand',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-product-brand.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-product-brand' => ['install','update','remove'],
        'theme/admin/product/brand' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'product-brand' => NULL
            ],
            [
                'admin-site-meta' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'lib-formatter' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminProductBrand\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-product-brand/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminProductBrand' => [
                'path' => [
                    'value' => '/product/brand'
                ],
                'method' => 'GET',
                'handler' => 'AdminProductBrand\\Controller\\Brand::index'
            ],
            'adminProductBrandEdit' => [
                'path' => [
                    'value' => '/product/brand/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminProductBrand\\Controller\\Brand::edit'
            ],
            'adminProductBrandRemove' => [
                'path' => [
                    'value' => '/product/brand/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminProductBrand\\Controller\\Brand::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'product' => [
                    'label' => 'Product',
                    'icon' => '<i class="fas fa-box-open"></i>',
                    'priority' => 0,
                    'filterable' => false,
                    'children' => [
                        'brand' => [
                            'label' => 'Brand',
                            'icon'  => '<i></i>',
                            'route' => ['adminProductBrand'],
                            'perms' => 'manage_product_brand'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.product.edit' => [
                'brand' => [
                    'label' => 'Brand',
                    'type' => 'select',
                    'rules' => []
                ]
            ],
            'admin.product-brand.edit' => [
                '@extends' => ['std-site-meta'],
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => true
                    ]
                ],
                'slug' => [
                    'label' => 'Slug',
                    'type' => 'text',
                    'slugof' => 'name',
                    'rules' => [
                        'required' => TRUE,
                        'empty' => FALSE,
                        'unique' => [
                            'model' => 'ProductBrand\\Model\\ProductBrand',
                            'field' => 'slug',
                            'self' => [
                                'service' => 'req.param.id',
                                'field' => 'id'
                            ]
                        ]
                    ]
                ],
                'image' => [
                    'label' => 'Logo',
                    'type' => 'image',
                    'form' => 'std-image',
                    'rules' => []
                ],
                'content' => [
                    'label' => 'About',
                    'type' => 'summernote',
                    'rules' => []
                ],
                'meta-schema' => [
                    'options' => ['ItemList' => 'ItemList']
                ]
            ],
            'admin.product-brand.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ]
        ]
    ]
];
