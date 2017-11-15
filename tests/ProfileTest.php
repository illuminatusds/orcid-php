<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @author    Darren Stephens <darren.stephesn@durham.ac.uk>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

use Orcid\Profile;
use Orcid\Oauth;
use \Mockery as m;

/**
 * Base ORCID profile tests
 */
class ProfileTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /**
     * The complete profile paths
     *
     * @var  string
     **/
    private $complete    = '';
    private $complete_api2 = '';

    /**
     * The basic profile path
     *
     * @var  string
     **/
    private $basic  = '';
    private $basic_api2 = '';

    /**
     * Sets up tests
     *
     * @return  void
     **/
    public function setup()
    {
        $this->complete = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'profile-complete.json';
        $this->basic    = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'profile-basic.json';
        $this->complete_api2 = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'profile-complete2.json';
        $this->basic_api2    = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'profile-basic2.json';
    }

    /**
     * Gets a sample profile
     *
     * @param   bool  $complete  Whether or not to return full or basic profile
     * @return  object
     **/
    public function profile($complete = true)
    {
        $oauth = m::mock('Orcid\Oauth');

        $complete = $complete ? 'complete' : 'basic';
        $contents = json_decode(file_get_contents($this->$complete));

        // Tell the oauth method to return an empty ORCID iD
        $oauth->shouldReceive('getOrcid')->andReturn('0000-0000-0000-0000');
        $oauth->shouldReceive('getProfile')->andReturn($contents);

        $profile = new Profile($oauth, '1.2');

        return $profile;
    }

    /**
     * Gets a sample API v2 profile
     *
     * @param   bool  $complete  Whether or not to return full or basic profile
     * @return  object
     **/
    public function profileApi2($complete_api2 = true)
    {
        $oauth = m::mock('Orcid\Oauth');

        $complete_api2 = $complete_api2 ? 'complete_api2' : 'basic_v2';
        $contents = json_decode(file_get_contents($this->$complete_api2));

        // Tell the oauth method to return an empty ORCID iD
        $oauth->shouldReceive('getOrcid')->andReturn('0000-0000-0000-0000');
        $oauth->shouldReceive('getProfile')->andReturn($contents);

        $profile = new Profile($oauth, '2.0');

        return $profile;
    }

    /**
     * Test to make sure we can get an orcid id
     *
     * @return  void
     **/
    public function testGetOrcidId()
    {
        $this->assertEquals(
            '0000-0000-0000-0000',
            $this->profile()->id(),
            'Failed to fetch properly formatted ID'
        );
    }

    /**
     * Test to make sure we can get a raw profile
     *
     * @return  void
     **/
    public function testGetRawProfile()
    {
        $contents = json_decode(file_get_contents($this->complete));

        $this->assertEquals($contents->{'orcid-profile'}, $this->profile()->raw(), 'Failed to fetch raw profile data');
    }

    /**
     * Test to make sure we can get a raw profile
     *
     * @return  void
     **/
    public function testGetRawApi2Profile()
    {
        $contents = json_decode(file_get_contents($this->complete_api2));

        $this->assertEquals($contents, $this->profileApi2()->raw(), 'Failed to fetch raw profile data');
    }

    /**
     * Test to make sure we can get a user bio
     *
     * @return  void
     **/
    public function testGetBio()
    {
        $contents = json_decode(file_get_contents($this->complete));

        $this->assertEquals($contents->{'orcid-profile'}->{'orcid-bio'}, $this->profile()->bio(), 'Failed to fetch bio from profile data');
    }

    /**
     * Test to make sure we can get a user bio
     *
     * @return  void
     **/
    public function testGetApi2Bio()
    {
        $contents = json_decode(file_get_contents($this->complete_api2));

        $this->assertEquals($contents->{'person'}, $this->profileApi2()->person(), 'Failed to fetch bio from profile data');
    }

    /**
     * Test to make sure we can get a user email
     *
     * @return  void
     **/
    public function testGetEmail()
    {
        $this->assertEquals('testuser@gmail.com', $this->profile()->email(), 'Failed to fetch email from profile data');
    }

    /**
     * Test to make sure we can get a user email
     *
     * @return  void
     **/
    public function testGetApi2Email()
    {
        $this->assertEquals('john_smith@genericurl.com', $this->profileApi2()->email(), 'Failed to fetch email from profile data');
    }

    /**
     * Test to make sure we can get a user name
     *
     * @return  void
     **/
    public function testGetName()
    {
        $this->assertEquals('Test User', $this->profile()->fullName(), 'Failed to fetch full name from profile data');
    }
}
