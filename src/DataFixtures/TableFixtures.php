<?php

namespace App\DataFixtures;

use App\Entity\AdherentDebtor;
use App\Entity\Invoice;
use App\Helper\ImportCsvHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;


class TableFixtures extends Fixture
{
    private $entityManager;

    private $header;

    private $data;

    private $manager;

    public function __construct(ObjectManager $manager, EntityManagerInterface $entityManager,array $header = null,array $data = null)
    {
        $this->manager = $manager;
        $this->entityManager = $entityManager;
        $this->header = $header;
        $this->data = $data;
    }

    public function getDependencies()
    {
        return [
            AdherentDebtor::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $finder->in(__DIR__ . '/../../data');
        $finder->name('*.csv');
        $finder->files();
        $finder->sortByName();

        foreach( $finder as $file ){
            $entity="";
            switch ($file->getBasename()) {
                case 'invoices.csv':
                    $entity = Invoice::class;
                    break;
                case 'adherent_debtors.csv':
                    $entity = AdherentDebtor::class;
                    break;
            }

            $this->getFileArray($entity, $file);

            print "Imported response: {$file} " . PHP_EOL;
        }
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

        self::createArrayObject($entityName);

        $this->header = null;
        $this->data = null;
        fclose($handle);
    }

    public function createArrayObject($entityName)
    {
        foreach ($this->data as $data) {
            $entity = new $entityName();
            foreach($data as $field => $value) {
                if (property_exists($entity, $field)) {
                    $classMetadata = $this->manager->getClassMetadata($entityName);

                    if ($classMetadata->hasAssociation($field) == true && is_numeric($value)) {
                        //var_dump("field => value ", $field . " => " .$value);
                        //var_dump("GET ", AdherentDebtor::class . "_" . $value);
                        $value = $this->getReference(AdherentDebtor::class . "_" . $value);
                    }

                    var_dump($field . " => " . $value);

                    if ($classMetadata->getName() == AdherentDebtor::class && $field == 'id') {
                        //var_dump("SET ", AdherentDebtor::class . "_" . $value);
                        $this->addReference(AdherentDebtor::class . "_" . $value, $entity);
                    }

                    $method = "set" . "" . ucfirst($field);
                    var_dump($method);
                    $entity->$method($value);
                }
            }
            $this->manager->persist($entity);

            $metadata = $this->manager->getClassMetadata(get_class($entity));
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        }
        $this->manager->flush();
    }
}
