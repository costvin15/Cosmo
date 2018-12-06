<?php

namespace App\Http\Controller;
use App\Mapper\User;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PhotoController
 * @package App\Http\Controller
 * @Controller
 * @Route("/photo")
 */
class PhotoController extends AbstractController
{
    private $pathStorageImage;
    private $defaultImage;

    /**
     * RegisterController constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        parent::__construct($ci);
        $this->pathStorageImage = $ci->get('settings')->get('storage.photo');
        $this->defaultImage = $this->pathStorageImage . DIRECTORY_SEPARATOR . 'default.png';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/get/user/{id}", alias="photo.get.user", middleware={"App\Http\Middleware\SessionMiddleware"})
     */
    public function getUser(Request $request, Response $response, array $args) {
        if ($args['id'] != "") {
            $id = $args['id'];

            $user = $this->_dm->getRepository(User::class)->find($id);
            if ($user == null) {
                $fileDefault = @file_get_contents($this->defaultImage);

                $response->write($fileDefault);
                return $response->withHeader('Content-Type', mime_content_type($this->defaultImage));
            }

            $fileImage = $this->pathStorageImage . DIRECTORY_SEPARATOR .  $id;
            if (!file_exists($fileImage)) {
                $fileDefault = @file_get_contents($this->defaultImage);

                $response->write($fileDefault);
                return $response->withHeader('Content-Type', mime_content_type($this->defaultImage));
            }

            $fileDefault = @file_get_contents($fileImage);

            $response->write($fileDefault);
            return $response->withHeader('Content-Type', mime_content_type($fileImage));

        } else {
            $fileDefault = @file_get_contents($this->defaultImage);

            $response->write($fileDefault);
            return $response->withHeader('Content-Type', mime_content_type($this->defaultImage));
        }
    }
}