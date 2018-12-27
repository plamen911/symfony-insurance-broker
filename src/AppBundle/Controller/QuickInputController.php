<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Bill;
use AppBundle\Entity\GreenCard;
use AppBundle\Entity\Insurer;
use AppBundle\Entity\Sticker;
use AppBundle\Entity\User;
use AppBundle\Service\CommonService;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use AppBundle\Service\Insurer\InsurerServiceInterface;
use AppBundle\Service\Profile\ProfileServiceInterface;
use AppBundle\Service\Sticker\StickerServiceInterface;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class QuickInputController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("quick-input")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class QuickInputController extends Controller
{
    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var StickerServiceInterface $stickerService */
    private $stickerService;

    /** @var CommonService $commonService */
    private $commonService;

    /** @var InsurerServiceInterface $insurerService */
    private $insurerService;

    /** @var ProfileServiceInterface $profileService */
    private $profileService;

    /**
     * StickerController constructor.
     * @param FormErrorServiceInterface $formErrorService
     * @param StickerServiceInterface $stickerService
     * @param CommonService $commonService
     * @param InsurerServiceInterface $insurerService
     */
    public function __construct(FormErrorServiceInterface $formErrorService, StickerServiceInterface $stickerService, CommonService $commonService, InsurerServiceInterface $insurerService, ProfileServiceInterface $profileService)
    {
        $this->formErrorService = $formErrorService;
        $this->stickerService = $stickerService;
        $this->commonService = $commonService;
        $this->insurerService = $insurerService;
        $this->profileService = $profileService;
    }

    /**
     * @Route("/{type}", methods={"GET", "POST"}, name="quick_input", requirements={"type": "sticker|green-card|bill"})
     * @param Request $request
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function quickInputAction(Request $request, string $type)
    {
        $backUrl = $this->generateUrl('sticker_index');
        $label = 'стикери';

        $form = $this->createQuickInputForm();
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();


        }

        return $this->render('quick-input/index.html.twig', [
            'backUrl' => $backUrl,
            'label' => $label,
            'type' => $type,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/suggest/{type}", methods={"POST"}, name="quick_input_suggest", requirements={"type": "sticker|green-card|bill"})
     * @param Request $request
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function suggestNumbersAction(Request $request, string $type)
    {
        $startIdNumber = $request->request->get('startIdNumber');
        $endIdNumber = $request->request->get('endIdNumber');
        $range = $this->commonService->generateCustomRange($startIdNumber, $endIdNumber);
        if (!count($range)) {
            return $this->json([
               'error' => 'Има нещо нередно в номерата, които сте въвели - ' . $startIdNumber . ' ' . $endIdNumber . '. Моля, проверете ги отново и опитайте пак.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json([
            'suggested' => implode(',', $range)
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/suggest/{type}/save", methods={"POST"}, name="quick_input_suggest_save", requirements={"type": "sticker|green-card|bill"})
     * @param Request $request
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function saveSuggestedAction(Request $request, string $type)
    {
        $suggested = $request->request->get('suggested') ?? null;
        $numbers = $suggested['numbers'] ?? null;
        $insurerId = $suggested['insurer'] ?? null;
        $agentId = $suggested['agent'] ?? null;
        $givenAt = $suggested['givenAt'] ?? null;
        if (null !== $givenAt) {
            try {
                $givenAt = DateTime::createFromFormat('d.m.Y', $givenAt);
            } catch (\Exception $ex) {
                $givenAt = null;
            }
        }

        $range = array_filter(explode(',', $numbers));
        $insurer = $this->insurerService->find($insurerId);
        if (null === $insurer) {
            $this->addFlash('danger', 'Застрахователят не може да бъде намерен!');
            return $this->redirectToRoute('quick_input', ['type' => $type]);
        }

        $agent = $this->profileService->find($agentId);

        $existing = $this->stickerService->getExistingByInsurerAndByRange($insurer, $range);
        $i = 0;
        foreach ($range as $idNumber) {
            if (empty($idNumber) || in_array($idNumber, $existing)) continue;

            $i++;
            if ('sticker' === $type) {
                $sticker = new Sticker();
                $sticker->setIdNumber($idNumber);
                $sticker->setInsurer($insurer);
                $sticker->setAgent($agent);
                $sticker->setGivenAt((null === $agent) ? null : $givenAt);
                $sticker->setReceivedAt(new \DateTime());
                $sticker->setAuthor($this->getUser());
                $this->stickerService->save($sticker);

            } elseif ('green-card' === $type) {
                $greenCard = new GreenCard();
                $greenCard->setIdNumber($idNumber);
                $greenCard->setInsurer($insurer);
                $greenCard->setAgent($agent);
                $greenCard->setGivenAt((null === $agent) ? null : $givenAt);
                $greenCard->setReceivedAt(new \DateTime());
                $greenCard->setAuthor($this->getUser());
                // todo: save

            } else {
                $bill = new Bill();
                $bill->setIdNumber($idNumber);
                $bill->setInsurer($insurer);
                $bill->setAgent($agent);
                $bill->setGivenAt((null === $agent) ? null : $givenAt);
                $bill->setReceivedAt(new \DateTime());
                $bill->setAuthor($this->getUser());
                // todo: save

            }
        }

        if ('sticker' === $type) {
            $this->addFlash('success', 'Успешно бяха въведени ' . $i . ' стикера.');
            return $this->redirectToRoute('sticker_index');

        } elseif ('green-card' === $type) {

        } else {

        }
    }


    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Exception
     */
    public function createQuickInputForm()
    {
        return $this->createFormBuilder()
            ->add('startIdNumber', TextType::class, [
                'label' => 'Начален No',
                'attr' => [
                    'class' => 'form-control-sm mr-2',
                    'placeholder' => 'Начален No',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Начален No е задължителен.']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Начален No трябва да съдържа поне {{ limit }} символа.'
                    ])
                ]
            ])
            ->add('endIdNumber', TextType::class, [
                'label' => 'Последен No (вкл.)',
                'attr' => [
                    'class' => 'form-control-sm mr-2',
                    'placeholder' => 'Последен No (вкл.)',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Последен No е задължителен.']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Последен No трябва да съдържа поне {{ limit }} символа.'
                    ])
                ]
            ])
            ->add('insurer', EntityType::class, [
                'class' => Insurer::class,
                'choice_label' => 'long_name',
                'placeholder' => '- избери -',
                'label' => 'Застраховател',
                'attr' => [
                    'class' => 'form-control-sm'
                ],
                'constraints' => new NotBlank(['message' => 'Застраховател е задължително поле.'])
            ])
            ->add('agent', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'full_name',
                'placeholder' => '- избери -',
                'label' => 'Агент',
                'attr' => [
                    'class' => 'form-control-sm'
                ]
                // todo: filter active users only
            ])
            ->add('givenAt', DateType::class, [
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control-sm js-datepicker',
                    'placeholder' => 'Дата на предаване'
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата на предаване на агента'
            ])
            ->getForm();
    }
}
