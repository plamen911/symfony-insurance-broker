<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Insurer;
use AppBundle\Entity\User;
use AppBundle\Service\Bill\BillServiceInterface;
use AppBundle\Service\CommonService;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use AppBundle\Service\GreenCard\GreenCardServiceInterface;
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

    /** @var CommonService $commonService */
    private $commonService;

    /** @var InsurerServiceInterface $insurerService */
    private $insurerService;

    /** @var ProfileServiceInterface $profileService */
    private $profileService;

    /**
     * StickerController constructor.
     * @param FormErrorServiceInterface $formErrorService
     * @param CommonService $commonService
     * @param InsurerServiceInterface $insurerService
     * @param ProfileServiceInterface $profileService
     */
    public function __construct(FormErrorServiceInterface $formErrorService, CommonService $commonService, InsurerServiceInterface $insurerService, ProfileServiceInterface $profileService)
    {
        $this->formErrorService = $formErrorService;
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
        if ('sticker' === $type) {
            $label = 'стикери';
            $backUrl = $this->generateUrl('sticker_index');
        } elseif ('green-card' === $type) {
            $label = 'зелени карти';
            $backUrl = $this->generateUrl('sticker_index');
        } else {
            $label = 'сметки';
            $backUrl = $this->generateUrl('bill_index');
        }

        $form = $this->createQuickInputForm();

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
     * @param StickerServiceInterface $stickerService
     * @param GreenCardServiceInterface $greenCardService
     * @param BillServiceInterface $billService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function saveSuggestedAction(Request $request, string $type, StickerServiceInterface $stickerService, GreenCardServiceInterface $greenCardService, BillServiceInterface $billService)
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

        if ('sticker' === $type) {
            $stickerService->saveSuggested($insurer, $agent, $givenAt, $range);
            $this->addFlash('success', 'Стикерите бяха въведени успешно.');
            return $this->redirectToRoute('sticker_index');

        } elseif ('green-card' === $type) {
            $greenCardService->saveSuggested($insurer, $agent, $givenAt, $range);
            $this->addFlash('success', 'Зелените карти бяха въведени успешно.');
            // return $this->redirectToRoute('green_card_index');

        } else {
            $billService->saveSuggested($insurer, $agent, $givenAt, $range);
            $this->addFlash('success', 'Сметките бяха въведени успешно.');
            return $this->redirectToRoute('bill_index');
        }
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Exception
     */
    private function createQuickInputForm()
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
