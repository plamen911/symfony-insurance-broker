<?php
declare(strict_types=1);

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Bill;
use AppBundle\Entity\GreenCard;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Policy;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Entity\User;
use AppBundle\Repository\BillRepository;
use AppBundle\Repository\GreenCardRepository;
use AppBundle\Repository\PolicyRepository;
use AppBundle\Repository\StickerRepository;
use AppBundle\Repository\TypeOfPolicyRepository;
use AppBundle\Service\Aws\UploadInterface;
use AppBundle\Service\Policy\PolicyService;
use AppBundle\Service\Policy\PolicyServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PolicyServiceTest
 * @package Tests\AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * https://github.com/symfony/security-core/blob/master/Tests/Validator/Constraints/UserPasswordValidatorTest.php
 */
class PolicyServiceTest extends TestCase
{
    const PASSWORD = 's3Cr3t';
    const SALT = '^S4lt$';
    const POLICY_NAME = 'test policy type';

    /** @var PolicyServiceInterface $policyService */
    private $policyService;

    protected function setUp()
    {
        parent::setUp();

        $user = $this->createUser();
        $tokenStorage = $this->createTokenStorage($user);
        $policyRepo = $this->createMock(PolicyRepository::class);

        $typeOfPolicy = new TypeOfPolicy();
        $typeOfPolicy->setIsDeleted(false);
        $typeOfPolicy->setPosition(1);
        $typeOfPolicy->setName(self::POLICY_NAME);

        $typeOfPolicyRepo = $this->createMock(TypeOfPolicyRepository::class);
        $typeOfPolicyRepo->expects($this->any())
            ->method('findOneBy')
            ->willReturn($typeOfPolicy);

        $greenCardRepo = $this->createMock(GreenCardRepository::class);
        $stickerRepo = $this->createMock(StickerRepository::class);
        $billRepo = $this->createMock(BillRepository::class);
        $uploadService = $this->createMock(UploadInterface::class);

        $this->policyService = new PolicyService($tokenStorage, $policyRepo, $typeOfPolicyRepo, $greenCardRepo, $stickerRepo, $billRepo, $uploadService);
    }

    public function testGetDefaultTypeOfPolicy()
    {
        $defaultPolicy = $this->policyService->getDefaultTypeOfPolicy();

        $this->assertEquals(self::POLICY_NAME, $defaultPolicy->getName(), 'Policy name is not correct.');
    }

    /**
     * @throws \Exception
     */
    public function test_validatePayments_with_empty_due_date_expects_exception()
    {
        $request = $this->createMock(Request::class);

        $payments = new ArrayCollection();
        /** @var Payment $payment */
        $payment = new Payment();
        $payment->setDueAt(null);
        $payments->add($payment);

        /** @var Policy $policy */
        $policy = new Policy();
        $policy->setPayments($payments);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Липсва дата за падеж No 1.');
        $this->policyService->newPolicy($request, $policy);
    }

    /**
     * @throws \Exception
     */
    public function test_validatePayments_with_empty_due_amount_expects_exception()
    {
        $request = $this->createMock(Request::class);

        $payments = new ArrayCollection();
        /** @var Payment $payment */
        $payment = new Payment();
        $payment->setDueAt(new \DateTime());
        $payments->add($payment);

        /** @var Policy $policy */
        $policy = new Policy();
        $policy->setPayments($payments);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Липсва дължима сума за падеж No 1.');
        $this->policyService->newPolicy($request, $policy);
    }

    /**
     * @throws \Exception
     */
    public function test_validatePayments_with_different_due_and_paid_amounts_expects_exception()
    {
        $request = $this->createMock(Request::class);

        $amountDue = 100;
        $amountPaid = 99;

        $payments = new ArrayCollection();
        /** @var Payment $payment */
        $payment = new Payment();
        $payment->setDueAt(new \DateTime());
        $payment->setAmountDue($amountDue);
        $payment->setAmountPaid($amountPaid);
        $payments->add($payment);

        /** @var Policy $policy */
        $policy = new Policy();
        $policy->setPayments($payments);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf('Платената сума (%.2f) за падеж No %d не може да е различна от дължимата сума (%.2f).', $amountPaid, 1, $amountDue));
        $this->policyService->newPolicy($request, $policy);
    }

    /**
     * @throws \Exception
     */
    public function test_validatePayments_with_empty_paid_date_expects_exception()
    {
        $request = $this->createMock(Request::class);

        $amountDue = 100;
        $amountPaid = 100;

        $payments = new ArrayCollection();
        /** @var Payment $payment */
        $payment = new Payment();
        $payment->setDueAt(new \DateTime());
        $payment->setAmountDue($amountDue);
        $payment->setAmountPaid($amountPaid);
        $payment->setPaidAt(null);
        $payments->add($payment);

        /** @var Policy $policy */
        $policy = new Policy();
        $policy->setPayments($payments);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Липсва дата на плащане за падеж No 1.');
        $this->policyService->newPolicy($request, $policy);
    }

    /**
     * @throws \Exception
     */
    public function test_validatePayments_total_due_is_not_equal_to_payments_total_expects_exception()
    {
        $request = $this->createMock(Request::class);

        $totalDue = 101;
        $amountDue = 100;
        $amountPaid = 100;

        $payments = new ArrayCollection();
        /** @var Payment $payment */
        $payment = new Payment();
        $payment->setDueAt(new \DateTime());
        $payment->setAmountDue($amountDue);
        $payment->setAmountPaid($amountPaid);
        $payment->setPaidAt(new \DateTime());
        $payments->add($payment);

        /** @var Policy $policy */
        $policy = new Policy();
        $policy->setPayments($payments);
        $policy->setTotal($totalDue);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Общо дължима премия (' . $policy->getTotal() . ') е различна от сумата на вноските (' . $amountDue . ').');
        $this->policyService->newPolicy($request, $policy);
    }

    /**
     * @throws \Exception
     */
    public function test_calculate()
    {
        $greenCard1 = new GreenCard();
        $greenCard1->setPrice(10);
        $greenCard1->setTax(2);

        $greenCard2 = new GreenCard();
        $greenCard2->setPrice(10);
        $greenCard2->setTax(2);

        $policy = new Policy();
        $policy->setAmount(100);
        $policy->setTaxes(2);
        $policy->setAmountGf(11.5);
        $policy->addGreenCard($greenCard1);

        $policy->calculate();
        $this->assertEquals(10.2, $policy->getGreenCardTotal());

        $policy->addGreenCard($greenCard2);
        $policy->calculate();
        $this->assertEquals(20.40, $policy->getGreenCardTotal());

        $bill1 = new Bill();
        $bill1->setPrice(10);
        $policy->addBill($bill1);

        $bill2 = new Bill();
        $bill2->setPrice(10);
        $policy->addBill($bill2);

        $policy->calculate();
        $this->assertEquals(20, $policy->getBillTotal());

        $this->assertEquals(153.9, $policy->getTotal());
    }

    /**
     * @throws \Exception
     */
    public function test_calculate_with_real_input()
    {
        $greenCard = new GreenCard();
        $greenCard->setPrice(12);
        $greenCard->setTax(2);

        $bill = new Bill();
        $bill->setPrice(10);

        $policy = new Policy();
        $policy->setAmount(100);
        $policy->setTaxes(2);
        $policy->setAmountGf(11.5);
        $policy->addGreenCard($greenCard);
        $policy->addBill($bill);

        $policy->calculate();

        $this->assertEquals(135.74, $policy->getTotal());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|User
     */
    protected function createUser()
    {
        $mock = $this->getMockBuilder(UserInterface::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue(static::PASSWORD))
        ;
        $mock
            ->expects($this->any())
            ->method('getSalt')
            ->will($this->returnValue(static::SALT))
        ;
        return $mock;
    }

    /**
     * @param null $user
     * @return \PHPUnit\Framework\MockObject\MockObject|TokenInterface|TokenStorageInterface|null
     */
    protected function createTokenStorage($user = null)
    {
        $token = $this->createAuthenticationToken($user);
        $mock = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token))
        ;
        return $mock;
    }

    /**
     * @param null $user
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createAuthenticationToken($user = null)
    {
        $mock = $this->getMockBuilder(TokenInterface::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user))
        ;
        return $mock;
    }
}
