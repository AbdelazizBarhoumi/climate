<?php

namespace Faker\Provider\en_US;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{lastName}} Travel Agency',
        '{{lastName}} Tours',
        '{{lastName}} & {{lastName}} Travel',
        '{{lastName}} Tourism Services',
        '{{lastName}} Adventures',
    ];

    protected static $catchPhraseWords = [
        [
            'Authentic', 'Cultural', 'Exotic', 'Adventure', 'Luxury', 'Budget', 'Family', 'Romantic', 'Eco-friendly', 'Sustainable', 'Local', 'Hidden', 'Iconic', 'Scenic', 'Historic', 'Relaxing', 'Thrilling', 'Memorable', 'Unforgettable', 'Bespoke',
        ],
        [
            'Tunisia', 'Mediterranean', 'Sahara', 'Carthage', 'Djerba', 'Sousse', 'Tunis', 'Hammamet', 'Tozeur', 'Kairouan', 'Bizerte', 'Monastir', 'Mahdia', 'Tabarka', 'Nabeul', 'El Jem', 'Douz', 'Gabes', 'Gafsa', 'Zarzis',
        ],
        [
            'experiences', 'journeys', 'escapes', 'getaways', 'vacations', 'trips', 'adventures', 'tours', 'excursions', 'retreats', 'safaris', 'cruises', 'holidays', 'outings', 'voyages', 'pilgrimages', 'explorations', 'discoveries', 'immersions', 'ramblings',
        ],
    ];

    protected static $bsWords = [
        [
            'aggregate', 'architect', 'benchmark', 'brand', 'cultivate', 'deliver', 'deploy', 'disintermediate', 'drive', 'e-enable', 'embrace', 'empower', 'enable', 'engage', 'engineer', 'enhance', 'envisioneer', 'evolve', 'expedite', 'exploit', 'extend', 'facilitate', 'generate', 'grow', 'harness', 'implement', 'incentivize', 'incubate', 'innovate', 'integrate', 'iterate', 'leverage', 'matrix', 'maximize', 'mesh', 'monetize', 'morph', 'optimize', 'orchestrate', 'productize', 'recontextualize', 'redefine', 'reengineer', 'reinvent', 'repurpose', 'revolutionize', 'scale', 'seize', 'strategize', 'streamline', 'syndicate', 'synergize', 'synthesize', 'target', 'transform', 'transition', 'unleash', 'utilize', 'visualize', 'whiteboard',
        ],
        [
            '24/7', 'B2B', 'B2C', 'back-end', 'best-of-breed', 'bleeding-edge', 'bricks-and-clicks', 'clicks-and-mortar', 'collaborative', 'compelling', 'cross-media', 'cross-platform', 'customer-focused', 'cutting-edge', 'distributed', 'dot-com', 'dynamic', 'e-business', 'efficient', 'end-to-end', 'enterprise', 'extensible', 'frictionless', 'front-end', 'global', 'granular', 'holistic', 'impactful', 'innovative', 'integrated', 'interactive', 'intuitive', 'killer', 'leading-edge', 'magnetic', 'mission-critical', 'next-generation', 'one-to-one', 'open-source', 'out-of-the-box', 'plug-and-play', 'proactive', 'real-time', 'revolutionary', 'rich', 'robust', 'scalable', 'seamless', 'sexy', 'sticky', 'strategic', 'synergistic', 'transparent', 'turn-key', 'ubiquitous', 'user-centric', 'value-added', 'vertical', 'viral', 'virtual', 'visionary', 'web-enabled', 'wireless', 'world-class',
        ],
        [
            'action-items', 'applications', 'architectures', 'bandwidth', 'channels', 'communities', 'convergence', 'deliverables', 'e-business', 'e-commerce', 'e-markets', 'e-services', 'e-tailers', 'experiences', 'eyeballs', 'functionalities', 'infomediaries', 'infrastructures', 'initiatives', 'interfaces', 'markets', 'methodologies', 'metrics', 'mindshare', 'models', 'networks', 'niches', 'paradigms', 'partnerships', 'platforms', 'portals', 'relationships', 'ROI', 'schemas', 'solutions', 'supply-chains', 'synergies', 'systems', 'technologies', 'users', 'vortals', 'web-readiness', 'webservices',
        ],
    ];

    /**
     * Source - http://www.careerplanner.com/ListOfJobs.cfm
     */
    protected static $jobTitleFormat = [
        'Tour Guide', 'Travel Agent', 'Tour Operator', 'Destination Specialist', 'Cultural Heritage Guide',
        'Adventure Tour Leader', 'Hotel Manager', 'Resort Coordinator', 'Spa Manager', 'Restaurant Manager',
        'Transportation Coordinator', 'Event Planner', 'Conference Organizer', 'Cruise Director', 'Excursion Manager',
        'Local Experience Curator', 'Heritage Site Guide', 'Wildlife Tour Guide', 'Eco-Tourism Specialist', 'Cultural Liaison',
        'Visa and Documentation Assistant', 'Customer Service Representative', 'Booking Agent', 'Reservation Specialist',
        'Group Coordinator', 'VIP Concierge', 'Airport Transfer Coordinator', 'Car Rental Agent', 'Itinerary Planner',
        'Marketing Coordinator', 'Sales Representative', 'Partnership Manager', 'Supplier Relations Manager', 'Quality Assurance Specialist',
        'Operations Manager', 'Logistics Coordinator', 'Safety and Compliance Officer', 'Training Coordinator', 'HR Manager',
        'Financial Controller', 'Accountant', 'IT Support Specialist', 'Web Developer', 'Content Creator',
        'Social Media Manager', 'Photographer', 'Videographer', 'Translator', 'Local Guide Trainer'
    ];

    protected static $companySuffix = ['Travel Agency', 'Tours', 'Tourism Services', 'Adventures', 'Travel Group', 'Tour Operators'];

    /**
     * @see https://www.irs.gov/businesses/small-businesses-self-employed/how-eins-are-assigned-and-valid-ein-prefixes
     */
    protected static $einPrefixes = [
        01, 02, 03, 04, 05, 06, 10, 11, 12, 13, 14, 15, 16, 20, 21, 22, 23, 24, 25, 26, 27, 30, 31, 32, 33, 34, 35, 36,
        37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65,
        66, 67, 68, 71, 72, 73, 74, 75, 76, 77, 80, 81, 82, 83, 84, 85, 86, 87, 88, 90, 91, 92, 93, 94, 95, 98, 99,
    ];

    /**
     * @example 'Robust full-range hub'
     */
    public function catchPhrase()
    {
        $result = [];

        foreach (static::$catchPhraseWords as &$word) {
            $result[] = static::randomElement($word);
        }

        return implode(' ', $result);
    }

    /**
     * @example 'integrate extensible convergence'
     */
    public function bs()
    {
        $result = [];

        foreach (static::$bsWords as &$word) {
            $result[] = static::randomElement($word);
        }

        return implode(' ', $result);
    }

    /**
     * Employer Identification Number (EIN)
     *
     * @see https://en.wikipedia.org/wiki/Employer_Identification_Number
     *
     * @example '12-3456789'
     */
    public static function ein()
    {
        $prefix = static::randomElement(static::$einPrefixes);
        $suffix = self::numberBetween(0, 9999999);

        return sprintf('%02d-%07d', $prefix, $suffix);
    }
}
