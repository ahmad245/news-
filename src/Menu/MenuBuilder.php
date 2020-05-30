<?php
namespace App\Menu;

use Knp\Menu\FactoryInterface;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', ['route' => 'home'])
        ->setLabelAttribute('class', 'icon-home');

        $menu->addChild('DASHBOARD', ['route' => 'admin_dashboard'])
        ->setLabelAttribute('class', '');


        
///////////////////////////////////////////////////////////////////////////
$menu->addChild('Administrateurs', [
    'label' => 'Administrateurs',
    'extras' => ['safe_label' => true],
    'childrenAttributes' => [
        'class' => 'menu_parametrage ',
    ]
]
)->setLabelAttribute('class', 'icon-users');

$menu['Administrateurs']->addChild('Liste',['route'=>'admin_user', 'routeParameters' => ['type' => 'admin']]);
$menu['Administrateurs']->addChild('Créer',['route'=>'admin_user_add', 'routeParameters' => ['type' => 'admin']]);

//////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
        $menu->addChild('Nouveautés', [
            'label' => 'Nouveautés',
            'extras' => ['safe_label' => true],
            'childrenAttributes' => [
                'class' => 'menu_parametrage ',
            ]
        ]
        )->setLabelAttribute('class', 'icon-cogs');

        
        $menu['Nouveautés']->addChild('Liste',['route'=>'admin_news', 'routeParameters' => ['type' => 'admin']]);
        $menu['Nouveautés'] ->addChild('Créer',['route'=>'news_add','routeParameters' => ['type' => 'admin']]);

       ////////////////////////////////////////////////////////////////////
       $menu->addChild('Plateformes', [
        'label' => 'Plateformes',
        'extras' => ['safe_label' => true],
        'childrenAttributes' => [
            'class' => 'menu_parametrage ',
        ]
    ]
    )->setLabelAttribute('class', 'icon-cogs');
  
    $menu['Plateformes']->addChild('Gérer',['route'=>'category_add', 'routeParameters' => ['type' => 'admin']]);

///////////////////////////////////////////////////////
        // ... add more children

        return $menu;
    }
}