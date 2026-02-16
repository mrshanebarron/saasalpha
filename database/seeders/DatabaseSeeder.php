<?php

namespace Database\Seeders;

use App\Models\{Tenant, User, Enquiry, Quote, QuoteLineItem, Project, ProjectMember, Deliverable, TimeEntry, ComplianceDocument, CpdRecord, DocumentTemplate, Notification, AuditLog, Subcontractor};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant
        $tenant = Tenant::create([
            'name' => 'Northbridge Engineering Consultants',
            'slug' => 'northbridge',
            'industry' => 'engineering',
            'phone' => '(416) 555-0190',
            'email' => 'info@northbridge-eng.ca',
            'address' => '200 Bay Street, Suite 1400',
            'city' => 'Toronto',
            'state' => 'Ontario',
            'country' => 'Canada',
            'postal_code' => 'M5J 2J2',
            'tax_id' => 'BN 123456789',
            'currency' => 'CAD',
            'timezone' => 'America/Toronto',
        ]);

        // Users
        $admin = User::create([
            'tenant_id' => $tenant->id, 'name' => 'David Chen', 'email' => 'david@northbridge-eng.ca',
            'password' => Hash::make('password'), 'role' => 'admin', 'job_title' => 'Principal Engineer & Founder',
            'phone' => '(416) 555-0191',
        ]);
        $manager = User::create([
            'tenant_id' => $tenant->id, 'name' => 'Sarah Mitchell', 'email' => 'sarah@northbridge-eng.ca',
            'password' => Hash::make('password'), 'role' => 'manager', 'job_title' => 'Senior Project Manager',
            'phone' => '(416) 555-0192',
        ]);
        $eng1 = User::create([
            'tenant_id' => $tenant->id, 'name' => 'James Park', 'email' => 'james@northbridge-eng.ca',
            'password' => Hash::make('password'), 'role' => 'staff', 'job_title' => 'Structural Engineer, P.Eng.',
            'phone' => '(416) 555-0193',
        ]);
        $eng2 = User::create([
            'tenant_id' => $tenant->id, 'name' => 'Emily Rodriguez', 'email' => 'emily@northbridge-eng.ca',
            'password' => Hash::make('password'), 'role' => 'staff', 'job_title' => 'Geotechnical Engineer, P.Eng.',
            'phone' => '(416) 555-0194',
        ]);
        $eng3 = User::create([
            'tenant_id' => $tenant->id, 'name' => 'Michael Thompson', 'email' => 'michael@northbridge-eng.ca',
            'password' => Hash::make('password'), 'role' => 'staff', 'job_title' => 'Environmental Engineer, EIT',
            'phone' => '(416) 555-0195',
        ]);
        $coord = User::create([
            'tenant_id' => $tenant->id, 'name' => 'Lisa Wang', 'email' => 'lisa@northbridge-eng.ca',
            'password' => Hash::make('password'), 'role' => 'staff', 'job_title' => 'Project Coordinator',
            'phone' => '(416) 555-0196',
        ]);

        $users = [$admin, $manager, $eng1, $eng2, $eng3, $coord];

        // Subcontractors
        $subs = [];
        $subData = [
            ['company_name' => 'Precision Drilling Inc.', 'contact_name' => 'Robert Kwan', 'email' => 'rkwan@precisiondrilling.ca', 'specialty' => 'Borehole Drilling', 'default_rate' => 185],
            ['company_name' => 'GeoTest Labs', 'contact_name' => 'Anna Petrov', 'email' => 'anna@geotestlabs.ca', 'specialty' => 'Soil & Material Testing', 'default_rate' => 150],
            ['company_name' => 'EnviroScan Solutions', 'contact_name' => 'Derek Huang', 'email' => 'derek@enviroscan.ca', 'specialty' => 'Environmental Monitoring', 'default_rate' => 165],
            ['company_name' => 'Northern Survey Co.', 'contact_name' => 'Marie Fontaine', 'email' => 'mfontaine@northernsurvey.ca', 'specialty' => 'Land Surveying', 'default_rate' => 175],
        ];
        foreach ($subData as $s) {
            $subs[] = Subcontractor::create(array_merge($s, ['tenant_id' => $tenant->id, 'status' => 'active', 'phone' => '(416) 555-0' . rand(200, 299)]));
        }

        // Enquiries
        $enquiries = [];
        $enqData = [
            ['reference' => 'ENQ-2026-001', 'client_name' => 'Marcus Webb', 'client_company' => 'Lakeview Development Corp.', 'client_email' => 'mwebb@lakeviewdev.ca', 'source' => 'referral', 'status' => 'converted', 'priority' => 'high', 'description' => 'Geotechnical investigation for proposed 12-storey residential tower at 450 Lakeshore Blvd. Requires Phase I ESA and foundation design recommendations.', 'project_type' => 'geotechnical', 'estimated_value' => 185000, 'assigned_to' => $manager->id],
            ['reference' => 'ENQ-2026-002', 'client_name' => 'Patricia Okonkwo', 'client_company' => 'City of Mississauga', 'client_email' => 'pokonkwo@mississauga.ca', 'source' => 'direct', 'status' => 'converted', 'priority' => 'normal', 'description' => 'Structural assessment of the Heritage Bridge on Dundas Street. Bridge deck showing signs of deterioration. Need load rating analysis and rehabilitation recommendations.', 'project_type' => 'structural', 'estimated_value' => 95000, 'assigned_to' => $admin->id],
            ['reference' => 'ENQ-2026-003', 'client_name' => 'Frank Rossi', 'client_company' => 'Rossi Construction Ltd.', 'client_email' => 'frossi@rossiconstruction.ca', 'source' => 'repeat', 'status' => 'qualified', 'priority' => 'normal', 'description' => 'Foundation engineering for new warehouse complex in Brampton industrial park. 3 buildings totaling 45,000 sqft. Tight timeline due to pre-leasing commitments.', 'project_type' => 'geotechnical', 'estimated_value' => 72000, 'assigned_to' => $eng2->id],
            ['reference' => 'ENQ-2026-004', 'client_name' => 'Jennifer Liu', 'client_company' => 'GreenPath Environmental', 'client_email' => 'jliu@greenpath.ca', 'source' => 'website', 'status' => 'new', 'priority' => 'low', 'description' => 'Phase II Environmental Site Assessment for former gas station property at 89 Queen St E. Property transaction conditional on clean ESA.', 'project_type' => 'environmental', 'estimated_value' => 45000, 'assigned_to' => null],
            ['reference' => 'ENQ-2026-005', 'client_name' => 'Ahmed Hassan', 'client_company' => 'Hassan Properties Inc.', 'client_email' => 'ahassan@hassanproperties.ca', 'source' => 'referral', 'status' => 'reviewing', 'priority' => 'high', 'description' => 'Structural design review for a 6-unit townhouse development. Architect has preliminary drawings. Need structural engineering for permit submission.', 'project_type' => 'structural', 'estimated_value' => 38000, 'assigned_to' => $eng1->id],
            ['reference' => 'ENQ-2026-006', 'client_name' => 'Rachel Kim', 'client_company' => 'Waterfront Condos Inc.', 'client_email' => 'rkim@waterfrontcondos.ca', 'source' => 'direct', 'status' => 'new', 'priority' => 'urgent', 'description' => 'Emergency shoring design for excavation adjacent to existing 8-storey building. Contractor reports ground movement. Need immediate site visit and temporary support design.', 'project_type' => 'structural', 'estimated_value' => 55000, 'assigned_to' => null],
        ];
        foreach ($enqData as $e) {
            $enquiries[] = Enquiry::create(array_merge($e, ['tenant_id' => $tenant->id]));
        }

        // Quotes
        $q1 = Quote::create([
            'tenant_id' => $tenant->id, 'enquiry_id' => $enquiries[0]->id, 'reference' => 'QT-2026-001',
            'client_name' => 'Marcus Webb', 'client_company' => 'Lakeview Development Corp.',
            'status' => 'accepted', 'scope_of_work' => "Geotechnical Investigation Program:\n- Desktop study and historical review\n- 8 boreholes to 30m depth\n- Laboratory testing program (grain size, Atterberg limits, consolidation, triaxial)\n- Foundation design recommendations\n- Phase I Environmental Site Assessment\n- Final report with stamped drawings",
            'subtotal' => 162500, 'tax_rate' => 13.00, 'tax_amount' => 21125, 'total' => 183625,
            'valid_until' => now()->addDays(30), 'accepted_at' => now()->subDays(15),
            'terms' => "Payment: 25% upon authorization, 25% upon field completion, 50% upon report delivery.\nTimeline: 8-10 weeks from authorization.\nAll fees exclusive of HST.",
            'prepared_by' => $admin->id,
        ]);
        foreach ([
            ['description' => 'Desktop study & historical review', 'unit' => 'fixed', 'quantity' => 1, 'rate' => 8500, 'amount' => 8500],
            ['description' => 'Field investigation — 8 boreholes to 30m', 'unit' => 'each', 'quantity' => 8, 'rate' => 12500, 'amount' => 100000],
            ['description' => 'Laboratory testing program', 'unit' => 'fixed', 'quantity' => 1, 'rate' => 22000, 'amount' => 22000],
            ['description' => 'Foundation design & recommendations', 'unit' => 'fixed', 'quantity' => 1, 'rate' => 18000, 'amount' => 18000],
            ['description' => 'Phase I ESA', 'unit' => 'fixed', 'quantity' => 1, 'rate' => 14000, 'amount' => 14000],
        ] as $i => $li) {
            QuoteLineItem::create(array_merge($li, ['quote_id' => $q1->id, 'sort_order' => $i]));
        }

        $q2 = Quote::create([
            'tenant_id' => $tenant->id, 'enquiry_id' => $enquiries[1]->id, 'reference' => 'QT-2026-002',
            'client_name' => 'Patricia Okonkwo', 'client_company' => 'City of Mississauga',
            'status' => 'accepted', 'scope_of_work' => "Heritage Bridge Structural Assessment:\n- Visual inspection and condition survey\n- Material sampling and testing\n- Load rating analysis (CAN/CSA S6)\n- Finite element modeling\n- Rehabilitation options report\n- Cost estimating for recommended repairs",
            'subtotal' => 87500, 'tax_rate' => 13.00, 'tax_amount' => 11375, 'total' => 98875,
            'valid_until' => now()->addDays(30), 'accepted_at' => now()->subDays(22),
            'prepared_by' => $admin->id,
        ]);
        foreach ([
            ['description' => 'Visual inspection & condition survey', 'unit' => 'days', 'quantity' => 3, 'rate' => 4500, 'amount' => 13500],
            ['description' => 'Material sampling & lab testing', 'unit' => 'fixed', 'quantity' => 1, 'rate' => 15000, 'amount' => 15000],
            ['description' => 'Load rating analysis (CAN/CSA S6)', 'unit' => 'fixed', 'quantity' => 1, 'rate' => 28000, 'amount' => 28000],
            ['description' => 'Finite element modeling', 'unit' => 'fixed', 'quantity' => 1, 'rate' => 18000, 'amount' => 18000],
            ['description' => 'Rehabilitation report & cost estimates', 'unit' => 'fixed', 'quantity' => 1, 'rate' => 13000, 'amount' => 13000],
        ] as $i => $li) {
            QuoteLineItem::create(array_merge($li, ['quote_id' => $q2->id, 'sort_order' => $i]));
        }

        $q3 = Quote::create([
            'tenant_id' => $tenant->id, 'enquiry_id' => $enquiries[2]->id, 'reference' => 'QT-2026-003',
            'client_name' => 'Frank Rossi', 'client_company' => 'Rossi Construction Ltd.',
            'status' => 'sent', 'scope_of_work' => "Foundation Engineering — Brampton Warehouse Complex:\n- Geotechnical investigation (6 boreholes)\n- Foundation design for 3 warehouse buildings\n- Slab-on-grade design\n- Pavement design for truck loading areas",
            'subtotal' => 68000, 'tax_rate' => 13.00, 'tax_amount' => 8840, 'total' => 76840,
            'valid_until' => now()->addDays(45), 'sent_at' => now()->subDays(3),
            'prepared_by' => $manager->id,
        ]);

        // Projects (from accepted quotes)
        $p1 = Project::create([
            'tenant_id' => $tenant->id, 'quote_id' => $q1->id, 'reference' => 'PRJ-2026-001',
            'name' => 'Lakeview Tower — Geotechnical Investigation', 'client_name' => 'Marcus Webb',
            'client_company' => 'Lakeview Development Corp.', 'status' => 'active',
            'project_type' => 'geotechnical', 'description' => 'Full geotechnical investigation program for proposed 12-storey residential tower including Phase I ESA.',
            'budget' => 162500, 'spent' => 58200, 'start_date' => now()->subDays(20),
            'target_date' => now()->addDays(40), 'progress' => 45,
            'project_manager_id' => $manager->id,
            'address' => '450 Lakeshore Blvd', 'city' => 'Toronto', 'state' => 'Ontario',
        ]);
        $p2 = Project::create([
            'tenant_id' => $tenant->id, 'quote_id' => $q2->id, 'reference' => 'PRJ-2026-002',
            'name' => 'Heritage Bridge — Structural Assessment', 'client_name' => 'Patricia Okonkwo',
            'client_company' => 'City of Mississauga', 'status' => 'active',
            'project_type' => 'structural', 'description' => 'Comprehensive structural assessment of Heritage Bridge on Dundas Street including load rating and rehabilitation recommendations.',
            'budget' => 87500, 'spent' => 31500, 'start_date' => now()->subDays(30),
            'target_date' => now()->addDays(25), 'progress' => 60,
            'project_manager_id' => $admin->id,
            'address' => 'Dundas Street Bridge', 'city' => 'Mississauga', 'state' => 'Ontario',
        ]);
        $p3 = Project::create([
            'tenant_id' => $tenant->id, 'reference' => 'PRJ-2025-012',
            'name' => 'Yonge & Eglinton — Foundation Monitoring', 'client_name' => 'UrbanCore Developments',
            'client_company' => 'UrbanCore Developments', 'status' => 'completed',
            'project_type' => 'geotechnical', 'description' => 'Foundation settlement monitoring program during construction of adjacent 30-storey tower.',
            'budget' => 45000, 'spent' => 42800, 'start_date' => now()->subMonths(6),
            'target_date' => now()->subDays(15), 'completed_date' => now()->subDays(10), 'progress' => 100,
            'project_manager_id' => $eng2->id,
            'city' => 'Toronto', 'state' => 'Ontario',
        ]);
        $p4 = Project::create([
            'tenant_id' => $tenant->id, 'reference' => 'PRJ-2025-009',
            'name' => 'Markham Commercial Park — Phase I & II ESA', 'client_name' => 'Sino-Pacific Holdings',
            'client_company' => 'Sino-Pacific Holdings', 'status' => 'on_hold',
            'project_type' => 'environmental', 'description' => 'Phase I and Phase II Environmental Site Assessment for 15-acre commercial development site. On hold pending client land acquisition.',
            'budget' => 78000, 'spent' => 24500, 'start_date' => now()->subMonths(2),
            'target_date' => now()->addMonths(2), 'progress' => 30,
            'project_manager_id' => $eng3->id,
            'city' => 'Markham', 'state' => 'Ontario',
        ]);

        // Project Members
        foreach ([$eng2, $eng3, $coord] as $u) { ProjectMember::create(['project_id' => $p1->id, 'user_id' => $u->id, 'role' => 'member', 'hourly_rate' => rand(125, 185)]); }
        ProjectMember::create(['project_id' => $p1->id, 'user_id' => $manager->id, 'role' => 'lead', 'hourly_rate' => 210]);
        foreach ([$eng1, $coord] as $u) { ProjectMember::create(['project_id' => $p2->id, 'user_id' => $u->id, 'role' => 'member', 'hourly_rate' => rand(125, 185)]); }
        ProjectMember::create(['project_id' => $p2->id, 'user_id' => $admin->id, 'role' => 'lead', 'hourly_rate' => 250]);

        // Deliverables
        $delivData = [
            [$p1->id, 'Desktop Study Report', 'report', 'approved', now()->subDays(10), now()->subDays(12), $eng2->id, $manager->id],
            [$p1->id, 'Borehole Logs (BH-1 through BH-8)', 'report', 'in_progress', now()->addDays(5), null, $eng2->id, null],
            [$p1->id, 'Laboratory Test Results', 'report', 'pending', now()->addDays(15), null, $eng3->id, null],
            [$p1->id, 'Foundation Design Recommendations', 'calculation', 'pending', now()->addDays(30), null, $eng2->id, null],
            [$p1->id, 'Phase I ESA Report', 'report', 'in_progress', now()->addDays(20), null, $eng3->id, null],
            [$p1->id, 'Final Geotechnical Report', 'report', 'pending', now()->addDays(40), null, $manager->id, null],
            [$p2->id, 'Condition Survey Report', 'inspection', 'approved', now()->subDays(15), now()->subDays(18), $eng1->id, $admin->id],
            [$p2->id, 'Material Test Results', 'report', 'approved', now()->subDays(5), now()->subDays(7), $eng1->id, $admin->id],
            [$p2->id, 'Load Rating Analysis', 'calculation', 'in_progress', now()->addDays(10), null, $eng1->id, null],
            [$p2->id, 'FEM Model & Results', 'calculation', 'pending', now()->addDays(15), null, $eng1->id, null],
            [$p2->id, 'Rehabilitation Options Report', 'report', 'pending', now()->addDays(25), null, $admin->id, null],
        ];
        foreach ($delivData as $i => $d) {
            Deliverable::create([
                'project_id' => $d[0], 'tenant_id' => $tenant->id, 'title' => $d[1], 'type' => $d[2],
                'status' => $d[3], 'due_date' => $d[4], 'delivered_date' => $d[5],
                'assigned_to' => $d[6], 'reviewed_by' => $d[7], 'sort_order' => $i,
                'revision' => $d[3] === 'approved' ? 'B' : 'A',
            ]);
        }

        // Time Entries (last 3 weeks)
        $categories = ['engineering', 'review', 'admin', 'travel', 'meeting'];
        $descriptions = [
            'engineering' => ['Borehole logging and soil classification', 'Foundation design calculations', 'FEM model development', 'Load rating analysis per CAN/CSA S6', 'Lab test data analysis', 'ESA report drafting'],
            'review' => ['Peer review of structural calculations', 'QA/QC check on borehole logs', 'Technical review of deliverable draft'],
            'admin' => ['Project coordination with client', 'Subcontractor scheduling', 'Filing and documentation'],
            'travel' => ['Site visit — Lakeshore Blvd', 'Site visit — Heritage Bridge', 'Travel to GeoTest Labs'],
            'meeting' => ['Client progress meeting', 'Internal project review', 'Safety briefing'],
        ];
        for ($day = 20; $day >= 0; $day--) {
            if (now()->subDays($day)->isWeekend()) continue;
            foreach ([$eng1, $eng2, $eng3, $manager, $coord] as $user) {
                $project = $user === $eng1 ? $p2 : ($user === $eng3 ? $p4 : $p1);
                $cat = $categories[array_rand($categories)];
                $desc = $descriptions[$cat][array_rand($descriptions[$cat])];
                TimeEntry::create([
                    'tenant_id' => $tenant->id, 'user_id' => $user->id, 'project_id' => $project->id,
                    'date' => now()->subDays($day), 'hours' => rand(40, 85) / 10,
                    'category' => $cat, 'description' => $desc, 'billable' => $cat !== 'admin',
                    'rate' => rand(125, 250), 'status' => $day > 7 ? 'approved' : 'submitted',
                ]);
            }
        }

        // Compliance Documents
        $compDocs = [
            ['title' => 'Professional Engineers Ontario License', 'type' => 'license', 'document_number' => 'PEO-2024-78543', 'issue_date' => now()->subMonths(8), 'expiry_date' => now()->addMonths(4), 'issuing_body' => 'PEO', 'holder_id' => $admin->id, 'is_critical' => true],
            ['title' => 'Professional Engineers Ontario License', 'type' => 'license', 'document_number' => 'PEO-2024-78544', 'issue_date' => now()->subMonths(6), 'expiry_date' => now()->addMonths(6), 'issuing_body' => 'PEO', 'holder_id' => $eng1->id, 'is_critical' => true],
            ['title' => 'Professional Engineers Ontario License', 'type' => 'license', 'document_number' => 'PEO-2024-78545', 'issue_date' => now()->subMonths(10), 'expiry_date' => now()->addMonths(2), 'issuing_body' => 'PEO', 'holder_id' => $eng2->id, 'is_critical' => true],
            ['title' => 'General Liability Insurance', 'type' => 'insurance', 'document_number' => 'GLI-NEC-2026', 'issue_date' => now()->subMonths(3), 'expiry_date' => now()->addMonths(9), 'issuing_body' => 'Intact Insurance', 'is_critical' => true],
            ['title' => 'Professional Liability (E&O) Insurance', 'type' => 'insurance', 'document_number' => 'PLI-NEC-2026', 'issue_date' => now()->subMonths(1), 'expiry_date' => now()->addMonths(11), 'issuing_body' => 'Chubb Insurance', 'is_critical' => true],
            ['title' => 'WHMIS 2015 Certification', 'type' => 'safety', 'document_number' => 'WHMIS-2025-1045', 'issue_date' => now()->subMonths(14), 'expiry_date' => now()->subDays(10), 'issuing_body' => 'CCOHS', 'holder_id' => $eng3->id, 'is_critical' => false],
            ['title' => 'Working at Heights Training', 'type' => 'safety', 'document_number' => 'WAH-2025-3322', 'issue_date' => now()->subMonths(18), 'expiry_date' => now()->addDays(15), 'issuing_body' => 'Ontario MOL', 'holder_id' => $eng1->id, 'is_critical' => false],
            ['title' => 'Drilling Permit — City of Toronto', 'type' => 'permit', 'document_number' => 'DP-TOR-2026-445', 'issue_date' => now()->subDays(25), 'expiry_date' => now()->addDays(65), 'issuing_body' => 'City of Toronto'],
            ['title' => 'General Liability Insurance', 'type' => 'insurance', 'issue_date' => now()->subMonths(2), 'expiry_date' => now()->addMonths(10), 'issuing_body' => 'Aviva', 'subcontractor_id' => $subs[0]->id],
            ['title' => 'CCIL Accreditation', 'type' => 'certification', 'document_number' => 'CCIL-2025-8890', 'issue_date' => now()->subMonths(5), 'expiry_date' => now()->addMonths(7), 'issuing_body' => 'CCIL', 'subcontractor_id' => $subs[1]->id],
        ];
        foreach ($compDocs as $cd) {
            $cd['tenant_id'] = $tenant->id;
            $cd['status'] = 'valid';
            $cd['reminder_days'] = 30;
            ComplianceDocument::create($cd);
        }

        // CPD Records
        $cpdData = [
            [$admin->id, 'Advanced Foundation Design — Canadian Geotechnical Society', 'course', 'CGS', 16, now()->subMonths(2), true],
            [$admin->id, 'Project Management for Engineers', 'seminar', 'OSPE', 8, now()->subMonths(5), true],
            [$eng1->id, 'Seismic Design of Structures (CSA S304)', 'course', 'CSA Group', 24, now()->subMonths(1), true],
            [$eng1->id, 'Bridge Engineering Conference 2025', 'conference', 'CSCE', 12, now()->subMonths(4), true],
            [$eng2->id, 'Slope Stability Analysis', 'course', 'GeoSlope', 16, now()->subMonths(3), true],
            [$eng2->id, 'Environmental Geotechnics Workshop', 'seminar', 'CGS', 4, now()->subMonths(6), true],
            [$eng3->id, 'Environmental Site Assessment Best Practices', 'course', 'CSA Group', 8, now()->subMonths(1), false],
            [$eng3->id, 'Contaminated Sites Management', 'self_study', null, 12, now()->subMonths(2), false],
            [$manager->id, 'PMP Renewal — Ethics & Leadership', 'course', 'PMI', 16, now()->subMonths(3), true],
        ];
        foreach ($cpdData as $c) {
            CpdRecord::create([
                'tenant_id' => $tenant->id, 'user_id' => $c[0], 'title' => $c[1], 'category' => $c[2],
                'provider' => $c[3], 'hours' => $c[4], 'completed_date' => $c[5], 'verified' => $c[6],
                'verified_by' => $c[6] ? $admin->id : null,
            ]);
        }

        // Document Templates
        DocumentTemplate::create([
            'tenant_id' => $tenant->id, 'name' => 'Geotechnical Report', 'type' => 'report',
            'content' => "GEOTECHNICAL INVESTIGATION REPORT\n\nProject: {{project_name}}\nReference: {{project_reference}}\nClient: {{client_name}}\nDate: {{date}}\n\n1.0 INTRODUCTION\n{{introduction}}\n\n2.0 SITE DESCRIPTION\n{{site_description}}\n\n3.0 FIELD INVESTIGATION\n{{field_investigation}}\n\n4.0 LABORATORY TESTING\n{{lab_testing}}\n\n5.0 SUBSURFACE CONDITIONS\n{{subsurface_conditions}}\n\n6.0 FOUNDATION RECOMMENDATIONS\n{{recommendations}}\n\n7.0 LIMITATIONS\nThis report has been prepared for the exclusive use of {{client_name}}.\n\nPrepared by: {{engineer_name}}, P.Eng.\nReviewed by: {{reviewer_name}}, P.Eng.",
            'variables' => ['project_name', 'project_reference', 'client_name', 'date', 'introduction', 'site_description', 'field_investigation', 'lab_testing', 'subsurface_conditions', 'recommendations', 'engineer_name', 'reviewer_name'],
            'created_by' => $admin->id,
        ]);
        DocumentTemplate::create([
            'tenant_id' => $tenant->id, 'name' => 'Structural Assessment Letter', 'type' => 'letter',
            'content' => "STRUCTURAL ASSESSMENT\n\nRef: {{reference}}\nDate: {{date}}\n\nTo: {{client_name}}\nRe: {{project_name}}\n\nDear {{client_name}},\n\nPlease find enclosed our structural assessment for the above-referenced project.\n\n{{body}}\n\nPlease do not hesitate to contact us.\n\nSincerely,\n{{engineer_name}}, P.Eng.",
            'variables' => ['reference', 'date', 'client_name', 'project_name', 'body', 'engineer_name'],
            'created_by' => $admin->id,
        ]);

        // Notifications
        $notifs = [
            [$admin->id, 'expiry_warning', 'License Expiring Soon', "Emily Rodriguez's PEO license expires in 60 days. Schedule renewal.", '/compliance'],
            [$admin->id, 'task_assigned', 'New Enquiry Assigned', 'Emergency shoring design enquiry from Waterfront Condos Inc. assigned to you.', '/enquiries'],
            [$manager->id, 'project_update', 'Borehole Logs In Progress', 'Emily has started borehole log compilation for Lakeview Tower project.', '/projects/1'],
            [$eng1->id, 'cpd_reminder', 'Working at Heights Expiring', 'Your Working at Heights certification expires in 15 days.', '/cpd'],
            [$eng3->id, 'expiry_warning', 'WHMIS Expired', 'Your WHMIS 2015 certification expired 10 days ago. Please renew immediately.', '/compliance'],
            [$manager->id, 'task_assigned', 'Time Entries Pending Approval', '12 time entries from last week are awaiting your approval.', '/time-tracking'],
        ];
        foreach ($notifs as $n) {
            Notification::create([
                'tenant_id' => $tenant->id, 'user_id' => $n[0], 'type' => $n[1],
                'title' => $n[2], 'message' => $n[3], 'action_url' => $n[4],
            ]);
        }

        // Audit Logs
        $actions = [
            [$admin->id, 'created', 'Project', $p1->id, 'Created project PRJ-2026-001'],
            [$admin->id, 'created', 'Project', $p2->id, 'Created project PRJ-2026-002'],
            [$manager->id, 'updated', 'Enquiry', $enquiries[0]->id, 'Status changed from qualified to converted'],
            [$admin->id, 'approved', 'Deliverable', 1, 'Approved Desktop Study Report — Rev B'],
            [$eng1->id, 'created', 'TimeEntry', 1, 'Logged 6.5 hours on Heritage Bridge'],
            [$manager->id, 'updated', 'Quote', $q1->id, 'Quote QT-2026-001 accepted by client'],
        ];
        foreach ($actions as $a) {
            AuditLog::create([
                'tenant_id' => $tenant->id, 'user_id' => $a[0], 'action' => $a[1],
                'model_type' => $a[2], 'model_id' => $a[3], 'notes' => $a[4],
                'ip_address' => '192.168.1.' . rand(10, 50),
            ]);
        }
    }
}
