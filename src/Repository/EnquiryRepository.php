<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Enquiry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @method Enquiry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enquiry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enquiry[]    findAll()
 * @method Enquiry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnquiryRepository extends ServiceEntityRepository
{
    /** @var \Symfony\Bridge\Doctrine\RegistryInterface $em */
    private $em;
    /** @var LoggerInterface */
    private $logger;

    /**
     * BooksRepository constructor.
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     */
     public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
     {
         parent::__construct($registry, Enquiry::class);
         $this->logger = $logger;
     }

    /**
     * Check if Enquiry is unique
     * @param $name, $mobile, $arrival_date, $airport, $flight_number
     * @return $response
     */
    public function checkDuplicates($name, $mobile, $arrival_date, $airport, $terminal, $flight_number)
    {
      // run query for duplicates check
      $qb = $this->createQueryBuilder('e')
                 ->andWhere('e.name = :name')
                 ->andWhere('e.mobile = :mobile')
                 ->andWhere('e.arrival_date = :arrival_date')
                 ->andWhere('e.airport = :airport')
                 ->andWhere('e.flight_number = :flight_number')
                 ->setParameter(':name', $name)
                 ->setParameter(':mobile', $mobile)
                 ->setParameter(':arrival_date', $arrival_date)
                 ->setParameter(':airport', $airport)
                 ->setParameter(':flight_number', $flight_number)
                 ->getQuery();

      $response = $qb->execute();
      return $response;
    }

    /**
     * Save Enquiry
     * @param $data
     * @return $response
     */
     public function saveEnquiry($name = null, $mobile = null, $arrival_date = null, $airport = null, $terminal = null, $flight_number = null)
     {
        /** @internal - Generate Date */
        $date = new \DateTime();
        $this->createDate = $date->format($arrival_date);

        /** @internal - verify if entry already exist in DB to avoid adding duplicated entries */
        $dupsCheck = $this->checkDuplicates($name, $mobile, $date, $airport, $terminal, $flight_number);
        $this->logger->info('Dups checked: '.json_encode($dupsCheck));

        if (empty($dupsCheck)) {
            $entityManager = $this->getEntityManager();
            $enquiry = new Enquiry();
            $enquiry->setName($name);
            $enquiry->setMobile($mobile);
            $enquiry->setArrivalDate($date);
            $enquiry->setAirport($airport);
            if (!is_null($terminal)) {
                $enquiry->setTerminal($terminal);
            }
            $enquiry->setFlightNumber($flight_number);
            $entityManager->persist($enquiry);
            $entityManager->flush();
            $response = 'Enquiry saved';
        } else {
            $response = 'Enquiry already exist in DB';
        }

     return $response;
     }

}
