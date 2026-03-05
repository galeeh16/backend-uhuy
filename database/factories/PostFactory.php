<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
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

        return [
            'post_title' => $this->faker->jobTitle,
            'location' => $this->faker->city . ', ' . $this->faker->country,
            'overview' => $this->faker->paragraph(10),
            'responsibilities' => $responsibilties,
            'requirements' => $requirements,
            'skills' => $skills,
            'experience_year' => $this->faker->numberBetween(1, 10),
            'employment_type' => $this->faker->randomElement(['full_time', 'work_from_home', 'remote', 'contract']),
            'level_type' => $this->faker->randomElement(['junior', 'middle', 'senior', 'head']),
            'salary' => $this->faker->numberBetween(5000000, 50000000), // Range gaji dalam angka
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }
}
