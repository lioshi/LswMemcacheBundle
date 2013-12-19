<?php

namespace Lsw\MemcacheBundle\Cache;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Symfony\Component\HttpKernel\Exception\HttpException;

class GetPageCache
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller peut être une classe ou une closure. Ce n'est pas
         * courant dans Symfony2 mais ça peut arriver.
         * Si c'est une classe, elle est au format array
         */
        if (!is_array($controller)) {
            // return;
        }

        throw new HttpException(405, 'Aaaaah');

        // if ($controller[0] instanceof TokenAuthenticatedController) {

        //     // gestion de l'accès - reconnaissance de l'utilisateur
        //     // route: /testa/api/{email}/{token}       token=sha256(privateKey+email+yyyy-mm-aa-hh-mm)
        //     // $email = $event->getRequest()->attributes->get('email');
        //     // $token = $event->getRequest()->attributes->get('token');

        //     // no call authorized in ajax
        //     if ( $event->getRequest()->isXmlHttpRequest() ) {
        //         throw new HttpException(405, 'Not in ajax');
        //     }

        //     // no authentification for get token
        //     $uri = $event->getRequest()->getUri();
        //     if(strpos($uri, '/api/token') !== false) {
        //         return;
        //     }    

        //     // no authentification for authorized environments
        //     // $apiParam = $this->container->getParameter('api'); // parameters.yml datas
        //     // $authorized_environments = $apiParam['authorized_environments'];
        //     // if(in_array($this->container->get('kernel')->getEnvironment(), $authorized_environments)) {
        //     //     return;
        //     //     // echo 'valid token: '.$waitedToken;
        //     //     // echo ' | ';
        //     //     // echo 'env: '.$this->container->get('kernel')->getEnvironment();
        //     //     //throw new HttpException(501, 'Development\'s environment. Please use Token '.$waitedToken.' in production environment.');
        //     // }

        //     // même chose mais dans le header HTTP
        //     $email = $event->getRequest()->headers->get('email');
        //     $token = $event->getRequest()->headers->get('token');

        //     if ($email == ''){
        //         throw new BadRequestHttpException("Header value email required!");
        //     } 
        //     if ($token == ''){
        //         throw new BadRequestHttpException('Header value token required!');
        //     } 

        //     // l'email existe en base?
        //     $em = $this->container->get('doctrine')->getManager();
        //     $user = $em->getRepository('UserUserBundle:User')->findOneByEmail($email);

        //     if ($user){
        //         $privateKey = $user->getApiSecretKey();
        //     } else {
        //         throw new UnauthorizedHttpException(null, 'Email unknown!');
        //     } 

        //     // le token généré est valide jusqu'à minuit
        //     $time = date('Y-m-d', time());

        //     $waitedToken = sha1($privateKey.'|'.$email.'|'.$time);

        //     if ($token != $waitedToken) {
        //         throw new UnauthorizedHttpException(null, 'This action needs valid email/token duo!');
        //     }


        // }
    }
}
