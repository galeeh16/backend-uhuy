<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $responsibilties = <<<'HTML'
                        <ul>
                        <li>Be involved in every step of the product design cycle from discovery to developer handoff and user acceptance testing.</li>
                        <li>Work with BAs, product managers, and tech teams to lead the Product Design.</li>
                        <li>Maintain quality of the design process and ensure that when designs are translated into code they accurately reflect the design specifications.</li>
                        <li>Accurately estimate design tickets during planning sessions.</li>
                        <li>Contribute to sketching sessions involving non-designers.</li>
                        <li>Create, iterate and maintain UI deliverables including sketch files, style guides, high fidelity prototypes, micro interaction specifications, and pattern libraries.</li>
                        <li>Ensure design choices are data led by identifying assumptions to test each sprint, and work with the analysts in your team to plan moderated usability test sessions.</li>
                        <li>Design pixel perfect responsive UI's and understand that adopting common interface patterns is better for UX than reinventing the wheel.</li>
                        <li>Present your work to the wider business at Show & Tell sessions.</li>
                        </ul>
                        HTML;
        
        $requirements = <<<'HTML'
                        <ul>
                        <li>4+ years of system administration experience with the Microsoft Server platform (2012/2016, Microsoft IIS, Active Directory).</li>
                        <li>3+ years of hands-on system administration experience with AWS (EC2, Elastic Load Balancing, Multi AZ, etc.).</li>
                        <li>4+ years of SQL Server, MySQL experience.</li>
                        <li>Working knowledge of encryption techniques and protocols, multi-factor authentication, data protection, penetration testing, security threats.</li>
                        <li>Bachelor's Degree, or 4+ years of hands-on IT experience.</li>
                        </ul>
                        HTML;

        $skills =  <<<'HTML'
                    <ul>
                    <li>Programming experience developing web applications with the Microsoft .NET stack and a basic knowledge of SQL.</li>
                    <li>Development experience with Angular, Node.JS, or ColdFusion.</li>
                    <li>HTML, CSS, XHTML, XML.</li>
                    <li>Hypervisors, SANs, load balancers, firewalls, and Web Application Firewall (WAF).</li>
                    <li>Experience with Higher Logic (a collaboration platform).</li>
                    <li>MongoDB, Drupal.</li>
                    <li>Mobile App Development (iOS and Android).</li>
                    <li>Episerver CMS.</li>
                    <li>Microsoft Team Foundation Server.</li>
                    <li>Speaker Management System (Planstone).</li>
                    </ul>
                    HTML;

        // company random
        $company_id = DB::table('users')->where('role', 'COMPANY')->pluck('id');
        $company_array = $company_id->toArray();

        // level type random
        $level_array = ['junior', 'middle', 'senior', 'head'];
        // employment type random 
        $employment_array = ['full_time', 'work_from_home', 'remote', 'contract'];
        // location random
        $location_array = ['Jakarta', 'Bandung', 'Semarang', 'Pekanbaru', 'Medan', 'Makasar'];

        $array_insert = [];

        for ($i = 0; $i < 30; $i++) {

            $array_insert[] = [
                'id' => (string) Str::uuid(),
                'company_id' => $company_array[rand(0, count($company_array)-1)],
                'post_title' => fake()->jobTitle(),
                'location' => $location_array[rand(0, count($location_array)-1)],
                'overview' => 'As a Human Resources Coordinator, you will work within a Product Delivery Team fused with UX, engineering, product and data talent. You will help the team design beautiful interfaces that solve business challenges for our clients. We work with a number of Tier 1 banks on building web-based applications for AML, KYC and Sanctions List management workflows. This role is ideal if you are looking to segue your career into the FinTech or Big Data arenas.',
                'responsibilities' => $responsibilties,
                'requirements' => $requirements,
                'skills' => $skills,
                'experience_year' => rand(1,5),
                'employment_type' => $employment_array[rand(0, count($employment_array)-1)],
                'level_type' => $level_array[rand(0, count($level_array)-1)],
                'salary' => 15_000_000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        DB::table('posts')->insert($array_insert);
    }
}
