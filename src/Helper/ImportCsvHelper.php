<?php

namespace App\Helper;

use App\Entity\AdherentDebtor;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class ImportCsvHelper implements DependentFixtureInterface
{
    public $header = null;

    public $data = array();

    public $manager;

    public $entityManager;

    public function __construct(ObjectManager $manager, EntityManagerInterface $entityManager)
    {
        $this->manager = $manager;
        $this->entityManager = $entityManager;
    }

    public function getDependencies()
    {
        return [
            AdherentDebtor::class,
        ];
    }

    public function getFileArray(string $entityName, $file)
    {
        print "Importing: {$file->getBasename()} " . PHP_EOL;

        if (($handle = fopen( $file->getPathname(), "r")) !== FALSE) {
            while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($this->header === null) {
                    $this->header = $data;
                } else {
                    $this->data[] = array_combine($this->header, $data);
                }
            }
        }
        $this->header = null;
        fclose($handle);

        return self::createArrayObject($entityName);
    }

    public function createArrayObject($entityName)
    {
        foreach ($this->data as $data) {
            $entity = new $entityName();
            foreach($data as $field => $value) {
                if (property_exists($entity, $field)) {
                    $classMetadata = $this->manager->getClassMetadata($entityName);
                    if ($entityName == AdherentDebtor::class && $classMetadata->isIdentifier($value)) {
                        $this->setReference($value, $entity);
                    }
                    if ($classMetadata->hasAssociation($field) == true && is_numeric($value)) {
                        var_dump("field => value ", $field . " => " .$value);

                        //$value = $this->entityManager->getRepository(AdherentDebtor::class)->find($value);
                    }
                    //class_metadata - doctrine
                    $method = "set" . "" . ucfirst($field);
                    $entity->$method($value);
                }
            }
            $this->manager->persist($entity);
        }

        $this->manager->flush();
        return true;
    }
}