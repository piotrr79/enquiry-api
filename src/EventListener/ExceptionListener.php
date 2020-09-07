<?php
declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Debug\Exception\ClassNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use \ErrorException as ErrorException;
use App\Validator\ResponseSetter;
use App\PropertyAccessor\ExceptionPropertyAccessor;
use Psr\Log\LoggerInterface;

/**
 * ExceptionListener - overwriting response on Exception event
 * @package  Enquiry Api
 * @author   Piotr Rybinski
 */
class ExceptionListener
{
    /** @var LoggerInterface $logger */
    private $logger;
    /** @var ResponseSetter $responseSetter */
    private $responseSetter;
    /** @var ExceptionPropertyAccessor $propertyAccessor */
    private $propertyAccessor;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->responseSetter = new ResponseSetter();
        $this->propertyAccessor = new ExceptionPropertyAccessor();
    }

    /**
     * onKernelException - Ovewrite exception to appropriate Http response
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        /** @internal - Log exception details to log file */
        $this->logger->error(json_encode('Exception: '.$exception->getCode(). ', '.$exception->getMessage()));
        $this->logger->error(json_encode('Exception Details: '.$exception));

        /** @internal - Check if getStatusCode method exist, if not set 400 */
        if (is_callable([$exception, 'getStatusCode'])) {
            $prop_accessor = $this->propertyAccessor->accessExceptionProperties($exception);
            $status_code = $prop_accessor['statusCode'];
        } else {
            $status_code = 400;
        }

        /** @internal - Set new Response */
        $response = new JsonResponse();

        /** @internal - Most common exceptions */
        if ($exception instanceof NotFoundHttpException) {
            $api_response = ' Not Found. '. $exception->getMessage();
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $status_code = 401;
            $api_response = ' '. $exception->getMessage();
        } elseif ($exception instanceof AccessDeniedException) {
            $api_response = ' Forbidden';
        } elseif ($exception instanceof BadRequestHttpException) {
            $api_response = ' Error: '. $exception->getMessage();
        } elseif ($exception instanceof HttpException) {
            $api_response = ' Error: '. $exception->getMessage();
        } elseif ($exception instanceof FileException) {
            $apiResponse = ' Bad Request';
        } elseif ($exception instanceof ClassNotFoundException) {
            $apiResponse = ' Bad Request - Class not found';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $api_response = ' Method Not Allowed. '. $exception->getMessage();
        } elseif ($exception instanceof HttpExceptionInterface) {
            $api_response = ' Error: '. $exception->getMessage();
            $response->headers->replace($exception->getHeaders());
        } elseif ($exception instanceof ErrorException) {
            $api_response = ' Error: '. $exception->getMessage();
        } elseif ($exception instanceof Exception) {
            $api_response = ' Error: '. $exception->getMessage();
        } else {
            $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $api_response = ' Internal Server Error';
        }

        /** @internal - Sends modified response object to the event */
        $content = $this->responseSetter->setResponse($status_code, $api_response);
        $response->setStatusCode($status_code);
        $response->setContent(json_encode($content));
        
        $event->setResponse($response);
    }
}
