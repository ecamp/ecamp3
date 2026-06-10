<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\Category;
use App\Entity\Checklist;
use App\Entity\ChecklistItem;
use App\Entity\ContentNode\ChecklistNode;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\ContentNode\ResponsiveLayout;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentNode\Storyboard;
use App\Entity\ContentType;
use App\Entity\Day;
use App\Entity\MaterialItem;
use App\Entity\MaterialList;
use App\Entity\Period;
use App\Entity\Profile;
use App\Entity\ScheduleEntry;
use App\Entity\User;
use App\Util\IdGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator;

class DataGeneratorService {
    private ?Generator $faker = null;
    private array $contentTypes = [];
    private ?User $addUserToCamp = null;

    // Statistics tracking
    private array $stats = [
        'camps' => 0,
        'periods' => 0,
        'days' => 0,
        'categories' => 0,
        'activities' => 0,
        'scheduleEntries' => 0,
        'contentNodes' => 0,
        'materialLists' => 0,
        'materialItems' => 0,
        'campCollaborations' => 0,
        'checklists' => 0,
        'checklistItems' => 0,
    ];

    // J+S specific data
    private array $campTypes = [
        'J+S Kurs',
    ];

    private array $activityCategories = [
        ['short' => 'A', 'name' => 'Ausbildung', 'color' => '#FD7A7A'],
        ['short' => 'ES', 'name' => 'Essen', 'color' => '#BBBBBB'],
        ['short' => 'LL', 'name' => 'Lernen und lehren', 'color' => '#4DBB52'],
        ['short' => 'WP', 'name' => 'Wahlprogramm', 'color' => '#90B7E4'],
    ];

    private array $activityNames = [
        // Morgenprogram (Morning program)
        'Morgenrunde', 'Morgengymnastik', 'Frühstück', 'Mittagessen kochen', 'Abendessen vorbereiten',

        // Tagesprogramm (Daily program)
        'Lagersport', 'Wasserpiele', 'Kletterwand', 'Orientierungslauf',
        'Wanderung', 'Naturkunde', 'Schwimmen', 'Klettern',

        // Abendprogramm (Evening program)
        'Abendessen', 'Lagerfeuer', 'Grillen', 'Bastelworkshop',
        'Theaterstück', 'Musikabend', 'Tanzabend', 'Karaoke',

        // Logistik und Organisation (Logistics and organization)
        'Zeltaufbau', 'Zeltabbau', 'Lagerplatz reinigen', 'Einkaufsliste erstellen',
        'Materialliste erstellen', 'Transport organisieren', 'Notfallplan erstellen',

        // Sicherheit und Erste Hilfe (Safety and first aid)
        'Erste-Hilfe-Kiste kontrollieren', 'Verbandsmaterial prüfen',
        'Rettungsweg kennen', 'Sicherheitsregeln besprechen', 'Notfallübung durchführen',

        // J+S spezifisch (J+S specific)
        'J+S Ausbildungsziele definieren', 'Lagerprogramm erstellen', 'Tagesprogramm planen',
        'Aktivitäten dokumentieren', 'Lagerleitungssitzung', 'Ressourcen planen',

        // Kommunikation (Communication)
        'Lagerprotokoll führen', 'Fotos machen', 'Bericht schreiben',
        'Elterninformationen senden', 'Teilnehmerzertifikate erstellen',

        // Weitere Aktivitäten (Other activities)
        'Naturbeobachtung', 'Sternenhimmelbeobachtung', 'Wetterschutz beachten',
        'Umweltschutzmaßnahmen', 'Lagerplatz erkunden', 'Geocaching aktivität',
        'Nachtwanderung vorbereiten', 'Nachtwanderung durchführen',
    ];

    private array $activityLocations = [
        // J+S typical locations
        'Sportplatz', 'Turnhalle', 'Schwimmbad', 'Kletterwand', 'Wiese',
        'See', 'Fluss', 'Berg', 'Wald', 'Alm', 'Wanderweg',
        'Lagerplatz', 'Zeltplatz', 'Hütte', 'Biwak', 'Schutzhütte',

        // Specific locations
        'J+S Zelt', 'Pfadiheim', 'Jugendherberge', 'Sportanlage',
        'Naturfreundehaus', 'Campwiese', 'Gipfel', 'Bergsee',
        'Zeltplatz Obwalden', 'Schlucht', 'Spielwiese', 'Klettergrube',
    ];

    private array $materialItems = [
        ['article' => 'Fussbälle', 'quantity' => 5, 'unit' => 'Stück'],
        ['article' => 'Volleybälle', 'quantity' => 3, 'unit' => 'Stück'],
        ['article' => 'Netz', 'quantity' => 1, 'unit' => 'Stück'],
        ['article' => 'Schlauchboote', 'quantity' => 4, 'unit' => 'Stück'],
        ['article' => 'Schwimmwesten', 'quantity' => 20, 'unit' => 'Stück'],
        ['article' => 'Seile', 'quantity' => 10, 'unit' => 'Meter'],
        ['article' => 'Karabiner', 'quantity' => 30, 'unit' => 'Stück'],
        ['article' => 'Helme', 'quantity' => 15, 'unit' => 'Stück'],
        ['article' => 'Erste-Hilfe-Set', 'quantity' => 2, 'unit' => 'Stück'],
        ['article' => 'Wasserkannen', 'quantity' => 5, 'unit' => 'Stück'],
        ['article' => 'Becher', 'quantity' => 50, 'unit' => 'Stück'],
        ['article' => 'Teller', 'quantity' => 50, 'unit' => 'Stück'],
        ['article' => 'Besteck', 'quantity' => 50, 'unit' => 'Stück'],
        ['article' => 'Gaskocher', 'quantity' => 3, 'unit' => 'Stück'],
        ['article' => 'Gasflaschen', 'quantity' => 6, 'unit' => 'Stück'],
        ['article' => 'Töpfe', 'quantity' => 5, 'unit' => 'Stück'],
        ['article' => 'Pfannen', 'quantity' => 3, 'unit' => 'Stück'],
        ['article' => 'Laternen', 'quantity' => 10, 'unit' => 'Stück'],
        ['article' => 'Batterien', 'quantity' => 20, 'unit' => 'Stück'],
        ['article' => 'Karten', 'quantity' => 5, 'unit' => 'Stück'],
        ['article' => 'Kompass', 'quantity' => 5, 'unit' => 'Stück'],
        ['article' => 'Zelte', 'quantity' => 10, 'unit' => 'Stück'],
        ['article' => 'Schlafsäcke', 'quantity' => 40, 'unit' => 'Stück'],
        ['article' => 'Isomatten', 'quantity' => 40, 'unit' => 'Stück'],
        ['article' => 'Bastelmaterial', 'quantity' => 1, 'unit' => 'Kiste'],
        ['article' => 'Farben', 'quantity' => 1, 'unit' => 'Kiste'],
        ['article' => 'Pinsel', 'quantity' => 20, 'unit' => 'Stück'],
        ['article' => 'Papier', 'quantity' => 10, 'unit' => 'Bögen'],
        ['article' => 'Stifte', 'quantity' => 50, 'unit' => 'Stück'],
        ['article' => 'Klebeband', 'quantity' => 5, 'unit' => 'Rollen'],
        ['article' => 'Scheren', 'quantity' => 10, 'unit' => 'Stück'],
    ];

    private array $checklistItems = [
        // Camp organization
        'Lagerplatz reservieren', 'Lagerplatz besichtigen', 'Lagerplatz mieten',
        'Lagerleitung wählen', 'Co-Leitung wählen', 'Küchenleitung wählen',
        'Teilnehmer einladen', 'Teilnehmerliste erstellen', 'Elternbrief schreiben',
        'Versicherung prüfen', 'Gesundheitszeugnisse sammeln', 'Ernährungskonzept erstellen',
        'Einkaufsliste erstellen', 'Materialliste erstellen', 'Transport organisieren',
        'Notfallplan erstellen', 'Erste-Hilfe-Koffer packen', 'Medikamente organisieren',
        'Wetterbericht checken', 'Ausrüstung prüfen', 'Zelte prüfen',
        'Kochutensilien prüfen', 'Sportgeräte prüfen', 'Sicherheitsausrüstung prüfen',

        // Food and kitchen
        'Frühstück planen', 'Mittagessen kochen', 'Abendessen vorbereiten',
        'Nachtessen organisieren', 'Snacks einkaufen', 'Getränke besorgen',
        'Küche aufbauen', 'Spülbecken organisieren', 'Abwasch organisieren',
        'Essensplan erstellen', 'Allergien beachten', 'Vegetarier berücksichtigen',

        // Program and activities
        'Morgenrunde durchführen', 'Morgengymnastik', 'Freizeitaktivität planen',
        'Sportprogramm erstellen', 'Wanderung planen', 'Nachtwanderung vorbereiten',
        'Lagerfeuer entfachen', 'Grillen organisieren', 'Bastelworkshop vorbereiten',
        'Theaterstück einüben', 'Tanzabend organisieren', 'Musikworkshop vorbereiten',

        // Safety and first aid
        'Erste-Hilfe-Kiste kontrollieren', 'Verbandsmaterial prüfen', 'Notfallübung durchführen',
        'Rettungsweg kennen', 'Sicherheitsregeln besprechen', 'Unfallmeldebogen erstellen',
        'Feuerschutz beachten', 'Wassersicherheitsregeln', 'Wetterwarnungen beachten',

        // Logistics and cleanup
        'Zeltaufbau durchführen', 'Zeltabbau planen', 'Lagerplatz reinigen',
        'Abfallentsorgung organisieren', 'Müll trennen', 'Umweltschutzmaßnahmen',
        'Materialverpackung organisieren', 'Transport zum Lagerplatz', 'Materialrückgabe planen',

        // Communication and documentation
        'Lagerprotokoll führen', 'Fotos machen', 'Bericht schreiben',
        'Elterninformationen senden', 'Teilnehmerzertifikate erstellen', 'Evaluation durchführen',
        'Feedback einholen', 'Dokumentation archivieren', 'Abschlussfeier organisieren',

        // Equipment and materials
        'Seile kontrollieren', 'Karabiner prüfen', 'Helme zählen',
        'Kletterausrüstung prüfen', 'Wanderkarten besorgen', 'Kompass kontrollieren',
        'Kochgeschirr reinigen', 'Geschirr spülen', 'Materialliste aktualisieren',
        'Werkzeug organisieren', 'Bastelmaterial vorbereiten', 'Sportgeräte bereitstellen',

        // J+S specific
        'J+S Ausbildungsziele definieren', 'Lagerprogramm erstellen', 'Tagesprogramm planen',
        'Aktivitäten dokumentieren', 'Lagerleitungssitzung', 'Ressourcen planen',
        'Aufgaben verteilen', 'Verantwortlichkeiten klären', 'Absprachen halten',
    ];

    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * @psalm-suppress InvalidArgument
     */
    public function initialize(int $seed, string $addUserToCamp): void {
        if (null === $this->faker) {
            $this->faker = FakerFactory::create('de_CH');
            $this->faker->seed($seed);
        }

        $contentTypes = $this->entityManager
            ->getRepository(ContentType::class)
            ->findAll()
        ;
        $this->contentTypes = array_combine(
            array_column($contentTypes, 'name'),
            $contentTypes
        );

        $this->addUserToCamp = $this->findUserToAdd($addUserToCamp);
    }

    public function createRealisticCamp(int $activitiesPerCamp = 20, int $scheduleEntriesPerActivity = 1): Camp {
        $owner = $this->createUser();

        $camp = new Camp();
        $camp->shortTitle = $this->faker->randomElement($this->campTypes).' '.$this->faker->numberBetween(2023, 2026);
        $camp->title = $this->faker->sentence(3);
        $camp->motto = $this->faker->sentence(6);
        $camp->addressName = $this->faker->company();
        $camp->addressStreet = $this->faker->streetAddress();
        $camp->addressZipcode = $this->faker->postcode();
        $camp->addressCity = $this->faker->city();
        $camp->owner = $owner;
        $camp->creator = $owner;
        $camp->isPrototype = false;
        $camp->isShared = false;
        $camp->randomlyGenerated = true;

        $this->entityManager->persist($camp);
        ++$this->stats['camps'];

        $period = $this->createPeriod($camp);
        ++$this->stats['periods'];

        $categories = $this->createCategories($camp);
        $this->stats['categories'] += count($categories);

        $materialLists = $this->createMaterialLists($camp);
        $this->stats['materialLists'] += count($materialLists);

        $checklistItems = $this->createChecklists($camp);

        $activities = $this->createActivities($camp, $categories, $activitiesPerCamp, $period, $scheduleEntriesPerActivity, $checklistItems);
        $this->stats['activities'] += count($activities);

        $this->createCampCollaborations($camp, $owner);
        ++$this->stats['campCollaborations'];

        return $camp;
    }

    public function getStatistics(): array {
        return $this->stats;
    }

    private function findUserToAdd($addUserToCamp): User {
        $profileRepository = $this->entityManager->getRepository(Profile::class);
        $profile = $profileRepository->findOneBy(['email' => $addUserToCamp]);

        if (null === $profile) {
            throw new \RuntimeException("{$addUserToCamp} profile not found. Maybe you need run dev-data migrations first.");
        }

        return $profile->user;
    }

    private function createPeriod(Camp $camp): Period {
        $period = new Period();
        $period->camp = $camp;
        $period->description = $this->faker->sentence(4);

        // Random start date within next 2 years
        $startDate = $this->faker->dateTimeBetween('+1 month', '+2 years');
        $period->start = \DateTime::createFromInterface($startDate);

        // Period length between 5 and 10 days
        $duration = $this->faker->numberBetween(5, 10);
        $endDate = (clone $startDate)->modify("+{$duration} days");
        $period->end = \DateTime::createFromInterface($endDate);

        $this->entityManager->persist($period);

        // Create days
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $day = new Day();
            $day->period = $period;
            $day->dayOffset = (int) $startDate->diff($currentDate)->days;
            $this->entityManager->persist($day);
            ++$this->stats['days'];

            $currentDate->modify('+1 day');
        }

        return $period;
    }

    private function createCategories(Camp $camp): array {
        $categories = [];

        foreach ($this->activityCategories as $categoryData) {
            $category = new Category();
            $category->camp = $camp;
            $category->short = $categoryData['short'];
            $category->name = $categoryData['name'];
            $category->color = $categoryData['color'];
            $category->numberingStyle = $this->faker->randomElement(['1', 'A', 'I', 'a', 'i']);

            $this->entityManager->persist($category);
            $categories[] = $category;

            $rootContentNode = new ColumnLayout();
            $rootContentNode->root = $rootContentNode;
            $rootContentNode->parent = null;
            $rootContentNode->slot = null;
            $rootContentNode->position = 0;
            $rootContentNode->contentType = $this->contentTypes['ColumnLayout'];
            $rootContentNode->instanceName = $categoryData['name'];
            $rootContentNode->data = json_decode(ColumnLayout::DATA_DEFAULT, true);

            $this->entityManager->persist($rootContentNode);
            $category->rootContentNode = $rootContentNode;
            ++$this->stats['contentNodes'];
        }

        return $categories;
    }

    private function createActivities(
        Camp $camp,
        array $categories,
        int $count,
        Period $period,
        int $scheduleEntriesPerActivity,
        array $checklistItems
    ): array {
        $activities = [];
        $days = $period->days->getValues();

        for ($i = 0; $i < $count; ++$i) {
            $activity = new Activity();
            $activity->camp = $camp;
            $activity->title = $this->faker->randomElement($this->activityNames);
            $activity->location = $this->faker->randomElement($this->activityLocations);

            $activity->category = $this->faker->randomElement($categories);

            $rootContentNode = new ColumnLayout();
            $rootContentNode->root = $rootContentNode;
            $rootContentNode->parent = null;
            $rootContentNode->slot = null;
            $rootContentNode->position = 0;
            $rootContentNode->contentType = $this->contentTypes['ColumnLayout'];
            $rootContentNode->instanceName = $activity->title;
            $rootContentNode->data = json_decode(ColumnLayout::DATA_DEFAULT, true);

            $this->entityManager->persist($rootContentNode);
            ++$this->stats['contentNodes'];

            $activity->rootContentNode = $rootContentNode;

            $this->addContentToActivity($rootContentNode, $camp, $checklistItems);

            $this->entityManager->persist($activity);
            $activities[] = $activity;
            $numberOfScheduleEntries = $this->faker->numberBetween(max(0, $scheduleEntriesPerActivity - 2), $scheduleEntriesPerActivity + 2);

            for ($j = 0; $j < $numberOfScheduleEntries; ++$j) {
                $this->createScheduleEntry($activity, $period, $days);
            }
        }

        return $activities;
    }

    private function addContentToActivity(ColumnLayout $rootContentNode, Camp $camp, array $checklistItems): void {
        $responsiveLayout = new ResponsiveLayout();
        $responsiveLayout->root = $rootContentNode;
        $responsiveLayout->parent = $rootContentNode;
        $responsiveLayout->slot = '1';
        $responsiveLayout->position = 0;
        $responsiveLayout->contentType = $this->contentTypes['ResponsiveLayout'];
        $responsiveLayout->instanceName = 'Responsive Content';

        $this->entityManager->persist($responsiveLayout);
        ++$this->stats['contentNodes'];

        $singleText = new SingleText();
        $singleText->root = $rootContentNode;
        $singleText->parent = $responsiveLayout;
        $singleText->slot = 'main';
        $singleText->position = 0;
        $singleText->contentType = $this->contentTypes['Notes'];
        $singleText->instanceName = 'Beschreibung';
        $singleText->data = ['text' => $this->faker->paragraphs(3, true)];

        $this->entityManager->persist($singleText);
        ++$this->stats['contentNodes'];

        $materialNode = new MaterialNode();
        $materialNode->root = $rootContentNode;
        $materialNode->parent = $responsiveLayout;
        $materialNode->slot = 'aside-top';
        $materialNode->position = 0;
        $materialNode->contentType = $this->contentTypes['Material'];
        $materialNode->instanceName = 'Material';

        $this->entityManager->persist($materialNode);
        ++$this->stats['contentNodes'];

        $numMaterialItems = $this->faker->numberBetween(2, 5);
        foreach ($this->faker->randomElements($this->materialItems, $numMaterialItems) as $itemData) {
            $materialItem = new MaterialItem();
            $materialItem->camp = $camp;
            $materialItem->article = $itemData['article'];
            $materialItem->quantity = $this->faker->numberBetween(1, $itemData['quantity']);
            $materialItem->unit = $itemData['unit'];
            $materialItem->materialNode = $materialNode;
            $this->entityManager->persist($materialItem);
            ++$this->stats['materialItems'];
        }

        $storyboard = new Storyboard();
        $storyboard->root = $rootContentNode;
        $storyboard->parent = $responsiveLayout;
        $storyboard->slot = 'aside-bottom';
        $storyboard->position = 0;
        $storyboard->contentType = $this->contentTypes['Storyboard'];
        $storyboard->instanceName = 'Programm';
        $storyboard->data = ['sections' => [
            ['position' => 1, 'column1' => '00:00', 'column2' => $this->faker->sentence(5)],
            ['position' => 2, 'column1' => '00:30', 'column2' => $this->faker->sentence(5)],
        ]];

        $this->entityManager->persist($storyboard);
        ++$this->stats['contentNodes'];

        if (count($checklistItems) > 0) {
            $checklistNode = new ChecklistNode();
            $checklistNode->root = $rootContentNode;
            $checklistNode->parent = $rootContentNode;
            $checklistNode->slot = '2';
            $checklistNode->position = 0;
            $checklistNode->contentType = $this->contentTypes['Checklist'];

            $numItems = $this->faker->numberBetween(3, min(8, count($checklistItems)));
            $selectedItems = $this->faker->randomElements($checklistItems, $numItems);
            foreach ($selectedItems as $item) {
                $checklistNode->addChecklistItem($item);
            }

            $this->entityManager->persist($checklistNode);
            ++$this->stats['contentNodes'];
        }
    }

    private function createScheduleEntry(Activity $activity, Period $period, array $days): ScheduleEntry {
        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->period = $period;
        $scheduleEntry->activity = $activity;

        $this->faker->randomElement($days);

        $startHour = $this->faker->numberBetween(8, 20);
        $duration = $this->faker->numberBetween(30, 180);
        $startOffset = $startHour * 60;
        $endOffset = $startOffset + $duration;

        $scheduleEntry->startOffset = $startOffset;
        $scheduleEntry->endOffset = $endOffset;

        $this->entityManager->persist($scheduleEntry);
        ++$this->stats['scheduleEntries'];

        return $scheduleEntry;
    }

    private function createMaterialLists(Camp $camp): array {
        $materialLists = [];

        // Create general material list
        $generalList = new MaterialList();
        $generalList->camp = $camp;
        $generalList->name = 'Allgemeine Materialliste';
        $this->entityManager->persist($generalList);
        $materialLists[] = $generalList;

        // Add some material items to general list
        foreach ($this->faker->randomElements($this->materialItems, 8) as $itemData) {
            $materialItem = new MaterialItem();
            $materialItem->camp = $camp;
            $materialItem->article = $itemData['article'];
            $materialItem->quantity = $this->faker->numberBetween(1, $itemData['quantity']);
            $materialItem->unit = $itemData['unit'];
            $materialItem->materialList = $generalList;
            $this->entityManager->persist($materialItem);
            ++$this->stats['materialItems'];
        }

        $shoppingList = new MaterialList();
        $shoppingList->camp = $camp;
        $shoppingList->name = 'Einkaufsliste';
        $this->entityManager->persist($shoppingList);
        $materialLists[] = $shoppingList;

        foreach ($this->faker->randomElements($this->materialItems, 4) as $itemData) {
            $materialItem = new MaterialItem();
            $materialItem->camp = $camp;
            $materialItem->article = $itemData['article'];
            $materialItem->quantity = $this->faker->numberBetween(1, $itemData['quantity']);
            $materialItem->unit = $itemData['unit'];
            $materialItem->materialList = $shoppingList;
            $this->entityManager->persist($materialItem);
            ++$this->stats['materialItems'];
        }

        return $materialLists;
    }

    private function createCampCollaborations(Camp $camp, User $owner): void {
        $managerCollaboration = new CampCollaboration();
        $managerCollaboration->camp = $camp;
        $managerCollaboration->user = $owner;
        $managerCollaboration->role = 'manager';
        $managerCollaboration->status = 'established';
        $managerCollaboration->inviteEmail = $owner->profile->email;
        $this->entityManager->persist($managerCollaboration);

        $sitCollaboration = new CampCollaboration();
        $sitCollaboration->camp = $camp;
        $sitCollaboration->user = $this->addUserToCamp;
        $sitCollaboration->role = 'manager';
        $sitCollaboration->status = 'established';
        $this->entityManager->persist($sitCollaboration);

        $numCollaborators = $this->faker->numberBetween(2, 8);
        for ($i = 0; $i < $numCollaborators; ++$i) {
            $collaborator = $this->createUser();
            $collaboration = new CampCollaboration();
            $collaboration->camp = $camp;
            $collaboration->user = $collaborator;
            $collaboration->role = $this->faker->randomElement(['member', 'guest']);
            $collaboration->status = 'established';
            $collaboration->inviteEmail = $collaborator->profile->email;
            $this->entityManager->persist($collaboration);
        }
    }

    /**
     * @return ChecklistItem[] Flat array of all created ChecklistItems
     */
    private function createChecklists(Camp $camp): array {
        $allChecklistItems = [];

        $jsChecklist = new Checklist();
        $jsChecklist->camp = $camp;
        $jsChecklist->name = 'J+S Ausbildungsziele';
        $this->entityManager->persist($jsChecklist);
        ++$this->stats['checklists'];

        $items = $this->createChecklistItems($jsChecklist, $this->faker->numberBetween(5, 8));
        $allChecklistItems = array_merge($allChecklistItems, $items);

        $generalChecklist = new Checklist();
        $generalChecklist->camp = $camp;
        $generalChecklist->name = 'Allgemeine Checkliste';
        $this->entityManager->persist($generalChecklist);
        ++$this->stats['checklists'];

        $items = $this->createChecklistItems($generalChecklist, $this->faker->numberBetween(4, 6));
        $allChecklistItems = array_merge($allChecklistItems, $items);

        $logisticsChecklist = new Checklist();
        $logisticsChecklist->camp = $camp;
        $logisticsChecklist->name = 'Logistik Checkliste';
        $this->entityManager->persist($logisticsChecklist);
        ++$this->stats['checklists'];

        $items = $this->createChecklistItems($logisticsChecklist, $this->faker->numberBetween(3, 5));

        return array_merge($allChecklistItems, $items);
    }

    /**
     * Create checklist items for a given checklist
     * Items are created with a realistic 3-level nested structure.
     *
     * @param Checklist $checklist The checklist to add items to
     * @param int       $itemCount Number of top-level items to create
     *
     * @return ChecklistItem[] Flat array of all created ChecklistItems (including children and grandchildren)
     */
    private function createChecklistItems(Checklist $checklist, int $itemCount): array {
        $allItems = [];

        $shuffledItems = $this->faker->shuffle($this->checklistItems);
        $itemIndex = 0;

        for ($i = 0; $i < $itemCount; ++$i) {
            if ($itemIndex >= count($shuffledItems)) {
                $itemIndex = 0;
            }

            $parentItem = new ChecklistItem();
            $parentItem->checklist = $checklist;
            $parentItem->parent = null;
            $parentItem->position = $i;
            $parentItem->text = $shuffledItems[$itemIndex++];
            $this->entityManager->persist($parentItem);
            ++$this->stats['checklistItems'];
            $allItems[] = $parentItem;

            $numChildren = $this->faker->numberBetween(2, 4);
            for ($j = 0; $j < $numChildren; ++$j) {
                if ($itemIndex >= count($shuffledItems)) {
                    $itemIndex = 0;
                }

                $childItem = new ChecklistItem();
                $childItem->checklist = $checklist;
                $childItem->parent = $parentItem;
                $childItem->position = $j;
                $childItem->text = $shuffledItems[$itemIndex++];
                $this->entityManager->persist($childItem);
                ++$this->stats['checklistItems'];
                $allItems[] = $childItem;

                $numGrandchildren = $this->faker->numberBetween(1, 3);
                for ($k = 0; $k < $numGrandchildren; ++$k) {
                    if ($itemIndex >= count($shuffledItems)) {
                        $itemIndex = 0;
                    }

                    $grandchildItem = new ChecklistItem();
                    $grandchildItem->checklist = $checklist;
                    $grandchildItem->parent = $childItem;
                    $grandchildItem->position = $k;
                    $grandchildItem->text = $shuffledItems[$itemIndex++];
                    $this->entityManager->persist($grandchildItem);
                    ++$this->stats['checklistItems'];
                    $allItems[] = $grandchildItem;
                }
            }
        }

        return $allItems;
    }

    private function createUser(): User {
        $profile = new Profile();
        $profile->firstname = $this->faker->firstName();
        $profile->surname = $this->faker->lastName();
        $profile->email = $this->faker->firstName().IdGenerator::generateRandomHexString(10).'@'.$this->faker->safeEmailDomain();
        $profile->nickname = $this->faker->userName();
        $profile->language = $this->faker->randomElement(['de', 'it', 'fr']);

        $user = new User();
        $user->state = User::STATE_ACTIVATED;
        $user->profile = $profile;
        $user->plainPassword = 'test';

        $this->entityManager->persist($profile);
        $this->entityManager->persist($user);

        return $user;
    }
}
