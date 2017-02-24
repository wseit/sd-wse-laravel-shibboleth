<?php

use Orchestra\Testbench\TestCase;
use StudentAffairsUwm\Shibboleth\Tests\Stubs\Setup;
use StudentAffairsUwm\Shibboleth\Entitlement;
use App\User;

class EntitlementTest extends TestCase
{
    use Setup;

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCanInvalidateConstructorParameter()
    {
        new Entitlement(1);
    }

    public function testCanCreateEntitlement()
    {
        $entitlement = "urn:mace:dir:entitlement:common-lib-terms;urn:mace:uark.edu:ADGroups:Computing Services:Bomgar Groups:Bomgar-WCOB;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:Old Security Groups:WCOB-TechCenter;urn:mace:uark.edu:ADGroups:Exchange Resource Units:UITS (University IT Services):UITS: TechPartners;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:WCOB-Intranet;urn:mace:uark.edu:ADGroups:walton:Groups:linux02_sudoers;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:WCOB-Users;urn:mace:uark.edu:ADGroups:Exchange Resource Units:WCOB (Walton College):WCOB: Conference Team";

        $entitlement = new Entitlement($entitlement);

        $this->assertInstanceOf(Entitlement::class, $entitlement);
    }

    public function testCanApplyEntitlements()
    {
        $user = new User;

        $entitlement = "urn:mace:dir:entitlement:common-lib-terms;urn:mace:uark.edu:ADGroups:Computing Services:Bomgar Groups:Bomgar-WCOB;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:Old Security Groups:WCOB-TechCenter;urn:mace:uark.edu:ADGroups:Exchange Resource Units:UITS (University IT Services):UITS: TechPartners;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:WCOB-Intranet;urn:mace:uark.edu:ADGroups:walton:Groups:linux02_sudoers;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:WCOB-Users;urn:mace:uark.edu:ADGroups:Exchange Resource Units:WCOB (Walton College):WCOB: Conference Team";

        $entitlement = new Entitlement($entitlement);

        $entitlement->applyTo($user);

        // TODO: fetch user groups and verify membership mapping
    }
}
