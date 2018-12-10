<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Car;
use AppBundle\Entity\Client;
use AppBundle\Entity\Insurer;
use AppBundle\Entity\TypeOfCar;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Utils\Cyr2Lat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 *
 * Class AppFixtures
 * @package App\DataFixtures
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadRoles($manager);
        $this->loadUsers($manager);
        $this->loadPolicyTypes($manager);
        $this->loadInsurers($manager);
        $this->loadCarTypes($manager);
        $this->loadCars($manager);
        $this->loadOwners($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadRoles(ObjectManager $manager)
    {
        foreach ($this->getRoleData() as [$name, $title, $position]) {
            $role = new Role();
            $role->setName($name);
            $role->setTitle($title);
            $role->setPosition($position);

            $manager->persist($role);
            $this->addReference($role->getName(), $role);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadUsers(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$email, $password, $fullName, $roles]) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setFullName($fullName);

            $roleArray = [];
            foreach ($roles as $role) {
                $roleArray[] = $this->getReference($role);
            }
            $user->setRoles($roleArray);

            $manager->persist($user);
            $this->addReference($user->getEmail(), $user);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadPolicyTypes(ObjectManager $manager)
    {
        foreach ($this->getPolicyTypeData() as [$name, $longName, $position]) {
            $policyType = new TypeOfPolicy();
            $policyType->setName($name);
            $policyType->setLongName($longName);
            $policyType->setPosition($position);

            $manager->persist($policyType);
            $this->addReference($policyType->getName(), $policyType);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadInsurers(ObjectManager $manager)
    {
        foreach ($this->getInsurerData() as [$name, $longName, $logo, $position, $policyTypes]) {
            $insurer = new Insurer();
            $insurer->setName($name);
            $insurer->setLongName($longName);
            $insurer->setLogo($logo);
            $insurer->setPosition($position);

            $policyTypeArray = [];
            foreach ($policyTypes as $policyType) {
                $policyTypeArray[] = $this->getReference($policyType);
            }
            $insurer->setPolicyTypes($policyTypeArray);

            $manager->persist($insurer);
            $this->addReference($insurer->getName(), $insurer);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCarTypes(ObjectManager $manager)
    {
        foreach ($this->getCarTypeData() as [$name, $position]) {
            $carType = new TypeOfCar();
            $carType->setName($name);
            $carType->setPosition($position);

            $manager->persist($carType);
            $this->addReference($carType->getName(), $carType);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCars(ObjectManager $manager)
    {
        foreach ($this->getCarData() as [$idNumber, $carMake, $carModel, $carTypeName]) {
            $car = new Car(new Cyr2Lat());
            $car->setIdNumber($idNumber);
            $car->setCarMake($carMake);
            $car->setCarModel($carModel);

            /** @var TypeOfCar $carType */
            $carType = $this->getReference($carTypeName);
            $car->setCarType($carType);

            $manager->persist($car);
            $this->addReference($car->getIdNumber(), $car);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadOwners(ObjectManager $manager)
    {
        foreach ($this->getOwnerData() as [$firstName, $middleName, $lastName, $idNumber, $cars]) {
            $owner = new Client();
            $owner->setFirstName($firstName);
            $owner->setMiddleName($middleName);
            $owner->setLastName($lastName);
            $owner->setIdNumber($idNumber);

//            foreach ($cars as $carIdNumber) {
//                /** @var Car $car */
//                $car = $this->getReference($carIdNumber);
//                $owner->addCar($car);
//            }

            $carArray = [];
            foreach ($cars as $car) {
                $carArray[] = $this->getReference($car);
            }
            $owner->setOwnerCars($carArray);

            $manager->persist($owner);
            $this->addReference($owner->getIdNumber(), $owner);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    private function getRoleData()
    {
        return [
            // $roleData = [$name, $title, $position];
            ['ROLE_ADMIN', 'Администратор', 1],
            ['ROLE_BROKER', 'Брокер', 2],
            ['ROLE_OFFICE', 'Офис-мениджър', 3],
            ['ROLE_USER', 'Клиент', 4]
        ];
    }

    /**
     * @return array
     */
    private function getUserData()
    {
        return [
            // $userData = [$email, $password, $fullName, $roles];
            ['teodor_daike@abv.bg', '1', 'Теодор Дайке', ['ROLE_ADMIN', 'ROLE_BROKER']],
            ['nin4eto_pleven@abv.bg', '1', 'Нина Цветанова', ['ROLE_OFFICE']],
            ['mariya_nankova@mail.bg', '1', 'Мария Нанкова', ['ROLE_OFFICE']],
        ];
    }

    /**
     * @return array
     */
    private function getPolicyTypeData()
    {
        return [
            // $policyTypeData = [$name, $longName, $position];
            ['Г.О.', 'Гражданска отговорност', 1],
            ['Каско', 'Авто каско', 2],
        ];
    }

    /**
     * @return array
     */
    private function getInsurerData()
    {
        return [
            // $insurerData = [$name, $longName, $logo, $position, $policyTypes];
            ['Алианц', 'ЗАД "Алианц България" АД', '', 1, ['Г.О.', 'Каско']],
            ['ОЗК', 'ЗАД "ОЗК Застраховане"', '', 2, ['Г.О.', 'Каско']],
            ['БУЛСТРАД', 'ЗАД "БУЛСТРАД ВИЕНА ИНШУРЪНС ГРУП"', '', 3, ['Г.О.', 'Каско']],
        ];
    }

    /**
     * @return array
     */
    private function getCarTypeData()
    {
        return [
            // $carTypeData = [$name, $position];
            ['Лек автомобил', 1],
            ['Товарен автомобил', 2],
            ['Мотопед', 3],
            ['Мотоциклет', 4],
            ['Триколка', 5],
            ['Багажно или къмпинг ремарке', 6],
            ['Товарно ремарке', 7],
            ['Седлови влекач', 8],
            ['Автобус', 9],
            ['Микробус', 10],
            ['Тролейбус', 11],
            ['Трамвайна мотриса', 12],
            ['Строителна техника', 13],
            ['Горска техника', 14],
            ['Земеделска техника', 15],
            ['Вътрешнозаводски транспорт', 16],
            ['Трактор', 17],
            ['Електрокар или мотокар', 18],
            ['Електромобил', 19],
        ];
    }

    /**
     * @return array
     */
    private function getCarData()
    {
        return [
            // $carData = [$idNumber, $carMake, $carModel, $carTypeName];
            ['EH2350AT', 'Опел', 'Астра', 'Лек автомобил'],
            ['EH6426KA', 'Лада', '2017', 'Лек автомобил'],
            ['EH0003KB', 'Мазда', '6', 'Лек автомобил'],
            ['EH7085EX', 'ШМИТЦ', 'SCS 24 13 62 E', 'Лек автомобил'],
            ['EH9803KA', 'Тойота', 'РАВ 4', 'Лек автомобил'],
        ];
    }

    /**
     * @return array
     */
    private function getOwnerData()
    {
        return [
            // $ownerData = [$firstName, $middleName, $lastName, $idNumber, $cars];
            ['Ангел', 'Борисов', 'Кошерски', '5310294047', ['EH2350AT', 'EH6426KA']],
            ['Славчо', 'Виденов', 'Марков', '8002153981', ['EH0003KB']],
            ['Георги', 'Красимиров', 'Чернев', '4407274068', ['EH7085EX', 'EH9803KA']],
        ];
    }
}
