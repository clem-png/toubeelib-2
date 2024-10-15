<?php

namespace toubeelib\core\services\praticien;

use Monolog\Level;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\SpecialiteDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ServicePraticien implements ServicePraticienInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    private LoggerInterface $logger;

    public function __construct(PraticienRepositoryInterface $praticienRepository, LoggerInterface $logger)
    {
        $this->praticienRepository = $praticienRepository;
        $this->logger = $logger;
    }

    public function createPraticien(InputPraticienDTO $p): PraticienDTO{
        try{

        $praticienExist = $this->praticienRepository->existPraticienByTel($p->tel);

        if($praticienExist){
            throw new ServicePraticienInvalidDataException("Praticien déjà existant");
        }

        if ($p->specialite !== null){
            $specialite = $this->getSpecialiteById($p->specialite->id);
            if (!$specialite) {
                throw new ServicePraticienInvalidDataException("Specialite pas trouvé");
            }
        }

        $praticien = new Praticien($p->nom,$p->prenom,$p->adresse,$p->tel);

        if($p->specialite !== null){
            $specialite = $this->getSpecialiteById($p->specialite->id);
            $praticien->setSpecialite(new Specialite($specialite->ID, $specialite->label, $specialite->description));
        }

        $id = $this->praticienRepository->save($praticien);
        $praticien->setID($id);
        $this->logger->log(Level::Info, "Creation Praticien : " . $id);

        }catch (Exception $e){
            throw new ServicePraticienInvalidDataException($e);
        }
        return new PraticienDTO($praticien);
    }

    public function getPraticienById(string $id): PraticienDTO
    {
        try {
            $praticien = $this->praticienRepository->getPraticienById($id);
            return new PraticienDTO($praticien);
        } catch(\Exception $e) {
            throw new ServicePraticienInvalidDataException('invalid Praticien ID');
        }
    }

    public function getPraticienByTel(string $tel): PraticienDTO
    {
        try {
            $praticien = $this->praticienRepository->getPraticienByTel($tel);
            return new PraticienDTO($praticien);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienInvalidDataException('invalid Praticien TEL');
        }
    }

    public function getSpecialiteById(string $id): SpecialiteDTO
    {
        try {
            $specialite = $this->praticienRepository->getSpecialiteById($id);
            return $specialite->toDTO();
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienInvalidDataException('invalid Specialite ID');
        }
    }

    public function searchPraticiens(?string $prenom = null, ?string $nom = null, ?string $tel = null, ?string $adresse = null): array
    {
        try {
            $praticiens = $this->praticienRepository->searchPraticiens($prenom, $nom, $tel, $adresse);

            return array_map(fn($praticien) => new PraticienDTO($praticien), $praticiens);
        } catch (Exception $e) {
            throw new ServicePraticienInvalidDataException($e);
        }
    }
}