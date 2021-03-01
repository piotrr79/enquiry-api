<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use App\Entity\Enquiry;
use App\Repository\EnquiryRepository;
use App\Validator\RequestValidator;

/**
* EnquiryController
* @package  Enquiry Api
* @author   Piotr Rybinski
*/
class EnquiryController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;
    /** @var ManagerRegistry */
    private $registry;
    /** @var MailerInterface */
    private $mailer;
    /** @var EnquiryRepository */
    private $enquiryRepository;
    /** @var RequestValidator */
    private $requestValidator;

    /**
     * EnquiryController constructor.
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     * @param MailerInterface $mailer
     */
    public function __construct(ManagerRegistry $registry, 
                                LoggerInterface $logger, 
                                MailerInterface $mailer,
                                EnquiryRepository $enquiryRepository,
                                RequestValidator $requestValidator)
    {
        $this->registry = $registry;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->enquiryRepository = $enquiryRepository;
        $this->requestValidator = $requestValidator;
    }

    /**
     * Send enquiry
     * @param Request $request
     * @return string
     * @Route("/enquiry", name="add_enquiry", methods={"POST"})
     */
    public function sendEnquiry(Request $request): JsonResponse
    {
        /** @internal - Get POST data */
        $data = $request->getContent();
        $this->logger->error('Request Content: '. ($data));
        /** @internal - Transform POST data into array */
        $data = json_decode($data, TRUE);
        $this->logger->error('Data decoded: '. json_encode($data));

        /** @internal - Extract values */
        $name = $data['name'];
        $mobile = $data['mobile'];
        $arrival_date = $data['arrival_date'];
        $airport = $data['airport'];
        if (array_key_exists('terminal', $data)) {
            $terminal = $data['terminal'];
        } else {
           $terminal = '';
        }
        $flight_number = $data['flight_number'];

        /** @internal - validate request */
        $this->validateRequest($name, $mobile, $arrival_date, $airport, $terminal, $flight_number);

        try {
            $results = $this->enquiryRepository->saveEnquiry($name, $mobile, $arrival_date, $airport, $terminal, $flight_number);
            $code = 200;
            $this->sendEmail($results);
        } catch(HttpException $e) {
            $this->logger->error('API save Enquiry: '. $e->getMessage());
            $results = $e->getMessage();
            $code = 400;
        }

        $response = new JsonResponse($results, $code);
        return $response;
    }

    /**
     * Get list of enquiries (admin)
     * @param Request $request
     * @return string
     * @Route("/enquiry", name="get_enquiries", methods={"GET"})
     */
    public function getEnquiries(Request $request): JsonResponse
    {
        try {
            $results = $this->registry->getRepository(Enquiry::class)->findBy([], ['id' => 'DESC']);
            $code = 200;
        } catch(HttpException $e) {
            $this->logger->error('API getEnquiries Error: '. $e->getMessage());
            $response = $e->getMessage();
            $code = 400;
        }

        $response = [];
        foreach ($results as $result) {
            $response[] = [$result->getName(),
                           $result->getMobile(),
                           $result->getArrivalDate(),
                           $result->getAirport(),
                           $result->getTerminal(),
                           $result->getFlightNumber(),
                          ];
        }

        $response = new JsonResponse($response, $code);
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );
        return $response;
    }

    /**
     * Validate request
     * @param $name, $mobile, $arrival_date, $airport, $flight_number
     * @return void
     */
    private function validateRequest(string $name, string $mobile, string $arrival_date, string $airport, string $terminal = null, string $flight_number): void
    {
          $this->requestValidator->validateName($name);
          $this->requestValidator->validateMobile($mobile);
          $this->requestValidator->validateArivalDate($arrival_date);
          $this->requestValidator->validateAirport($airport);
          $this->requestValidator->validateFlightNumber($flight_number);
    }

    /**
     * Send email with notification about enquiry
     * @param $results
     * @return void
     */
    private function sendEmail($results): void
    {
        $email = (new Email())
            ->from(new Address('taxi@enquiry.com'))
            ->to(new Address(getenv('DELIVERY_MAIL')))
            ->subject('New order for Taxi')
            ->text('Order details: '. json_encode($results))
            ->html('<p>Order details: '. json_encode($results) .'</p>');

        /** @internal - check if smtp is enabled */
        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Mail was not send: '. json_encode($e->getMessage()));
            // throw new HttpException(400, 'Email could not be send');
        }
    }
}
