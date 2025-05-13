<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Web\User;

use Application\Controller\Web\AbstractWebController;
use Application\Domain\User\DTO\LoginInput;
use Application\Domain\User\Service\LoginService;
use Application\Service\InputHandler\InputTransformer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class LoginController
 *
 * @author Romain Cottard
 */
class LoginController extends AbstractWebController
{
    public function __construct(
        private readonly InputTransformer $inputTransformer,
        private readonly LoginService $loginService,
    ) {}

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     */
    public function view(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return $this->getResponse($this->render('@app/user/login.html.twig'));
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     */
    public function signIn(ServerRequestInterface $serverRequest): ResponseInterface
    {
        [$input, $errors] = $this->inputTransformer->transform($serverRequest, LoginInput::class);

        if ($errors === []) {
            try {
                $this->loginService->login($input);
                $this->redirectToRoute('home');
            } catch (\Throwable $exception) {
                $errors['global'] = $exception->getMessage();
            }
        }

        var_export($errors);

        $this->getContext()
            ->add('errors', $errors)
        ;

        return $this->getResponse($this->render('@app/home/home.html.twig'));
    }
}
