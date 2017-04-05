<?php
namespace Application\Service;
use Zend\Session\Container; // We need this when using sessions
use User\Entity\User;
/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager
{
    /**
     * Auth service.
     * @var Zend\Authentication\Authentication
     */
    private $authService;
    
    /**
     * Url view helper.
     * @var Zend\View\Helper\Url
     */
    private $urlHelper;
    
    private $translator;
    
         /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Constructs the service.
     */
    public function __construct($authService, $urlHelper, $translator,$entityManager) 
    {
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
        
        $this->translator = $translator;
        
        $this->entityManager = $entityManager;
    }
    
    /**
     * This method returns menu items depending on whether user has logged in or not.
     */
    public function getMenuItems() 
    {
        
        $this->traduz();
    
        $url = $this->urlHelper;
        $traduzir=$this->translator;
        
        $items = [];
        
        $items[] = [
            'id' => 'home',
            'label' => $traduzir->translate('Home'),
            'link'  => $url('home')
        ];
        
        $items[] = [
            'id' => 'about',
            'label' => $traduzir->translate('About'),
         
                
                         'dropdown' => [
            
                    [
                        'id' => 'contactos',
                        'label' => $traduzir->translate('Contacts'),
                        'link' => $url('contactos')
                    ],
                    [
                        'id' => 'localizacao',
                        'label' => $traduzir->translate('Location'),
                        'link' => $url('localizacao')
                    ],
                    [
                        'id' => 'precos',
                        'label' => $traduzir->translate('Pricing'),
                        'link' => $url('precos')
                    ],
             
                    ],
        ];
        $items[] = [
            'id' => 'ads',
            'label' => $traduzir->translate('Ads'),
         
            'dropdown' => [
 
                    [
                        'id' => 'empregos',
                        'label' => $traduzir->translate('Jobs'),
                        
                        'link' => $url('empregos')
                    ],
      
             
                    ],
        ];
        $items[] = [
                'id' => 'language',
                'label' => $traduzir->translate('Language'),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'english',
                        'label' => $traduzir->translate('English'),
                        'link' => $url('application', ['action'=>'seten'])
                    ],
                    [
                        'id' => 'portugues',
                        'label' => $traduzir->translate('Portuguese'),
                        'link' => $url('application', ['action'=>'setpt'])
                    ],
                    ],
            ];
        
        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users.
        if (!$this->authService->hasIdentity()) {
            $items[] = [
                'id' => 'users',
                'label' => $traduzir->translate('Users'),              
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'login',
                        'label' => $traduzir->translate('Sign in'),
                        'link' => $url('login')
                    ],
                    [
                        'id' => 'register',
                        'label' => $traduzir->translate('Register Free'),
                        'link' => $url('register')
                    ],
                    
                ],
            ];
      
        } else {
             $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
                        if($user->getPerfil()==User::PERFIL_ADMIN)
                        {
            $items[] = [
                'id' => 'admin',
                'label' => $traduzir->translate('Admin'),
                'dropdown' => [
                    [
                        'id' => 'users',
                        'label' => $traduzir->translate('Manage Users'),
                        'link' => $url('users')
                    ],
         
        
    
                    [
                        'id' => 'empregospostsmanage',
                        'label' => $traduzir->translate('Manage Jobs Posts'),
                        'link' => $url('empregosposts', ['action'=>'admin'])
                    ],
      
                ]
            ];
                        }
            $items[] = [
                'id' => 'logout',
                'label' => $this->authService->getIdentity(),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'settings',
                        'label' => $traduzir->translate('Settings'),
                        'link' => $url('application', ['action'=>'settings'])
                    ],
                    [
                        'id' => 'logout',
                        'label' => $traduzir->translate('Sign out'),
                        'link' => $url('logout')
                    ],
                ]
            ];
        }
        
        return $items;
    }
    
    
        private function traduz()
    {
        
        $user_session = new Container('language');
        $lang = $user_session->lang;
        
        $translator = $this->translator;//$this->getServiceLocator()->get('translator');
        
        if (($lang=='') || ($lang==null))
        {
          $translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            
        }else
        {
            
         $translator->setLocale($user_session->lang);
        }
        
    }
}


