<?php
namespace Cerad\Bundle\AccountBundle\Tests\FOSUser;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicTest extends WebTestCase
{
    public function testCreateUser()
    {
        $client = static::createClient();
        
        $container = $client->getContainer();
        
        // Manager will not create until AccountUser is mapped in doctrine
        $manager = $container->get('fos_user.user_manager');
        
        // Verify which manager we get
        $this->assertEquals('FOS\UserBundle\Doctrine\UserManager',get_class($manager));
        
        // Verify the user
        $user = $manager->createUser();
        $this->assertEquals('Cerad\Bundle\AccountBundle\Entity\AccountUser',get_class($user));
        
        // Curoius about the provider
        // Does not work without a firewall? Yep.  The factory makes the provider service I guess
        $provider = $container->get('fos_user.user_provider.username_email');
        
        // Providers contain a UserManager
        $this->assertEquals('FOS\UserBundle\Security\EmailUserProvider',get_class($provider));
   }
   /* =====================================================
     * Rather strange
     * Initially it was redirecting on me but then stopped
     * I did restart apache
     */
    public function sestCrawler()
    {
        $client = static::createClient();
        
      //$client->followRedirects(true);
        
      //$crawler = $client->request('GET', 'http://local.account.org/account/register/');
        
        $crawler = $client->request('GET', '/account/register/');
        
        $this->assertEquals('Symfony\Component\DomCrawler\Crawler',get_class($crawler));
        
        // 'Redirecting to http://localhost/account/register/
        //$this->assertEquals('Symfony\Component\DomCrawler\Crawler',$crawler->text());
        
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Verification")')->count()
        );
        
    }
}

?>
