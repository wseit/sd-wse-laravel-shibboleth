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
    public function testCanInvalidateFindInStringParameter()
    {
        Entitlement::findInString(1);
    }

    public function testCanFindEntitlementsInString()
    {
        $sudoer = new Entitlement;
        $sudoer->name = 'urn:mace:uark.edu:ADGroups:walton:Groups:linux02_sudoers';
        $sudoer->save();

        $bomgar = new Entitlement;
        $bomgar->name = 'urn:mace:uark.edu:ADGroups:Computing Services:Bomgar Groups:Bomgar-WCOB';
        $bomgar->save();

        $expected = collect([$sudoer, $bomgar])->toArray();

        $absent = new Entitlement;
        $absent->name = 'urn:mace:uark.edu:ADGroups:Something Somesuch:Entitlement-User-Does-Not-Have';
        $absent->save();

        $entitlementString = 'urn:mace:dir:entitlement:common-lib-terms;urn:mace:uark.edu:ADGroups:Computing Services:Bomgar Groups:Bomgar-WCOB;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:Old Security Groups:WCOB-TechCenter;urn:mace:uark.edu:ADGroups:Exchange Resource Units:UITS (University IT Services):UITS: TechPartners;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:WCOB-Intranet;urn:mace:uark.edu:ADGroups:walton:Groups:linux02_sudoers;urn:mace:uark.edu:ADGroups:Walton College:Security Groups:WCOB-Users;urn:mace:uark.edu:ADGroups:Exchange Resource Units:WCOB (Walton College):WCOB: Conference Team';

        $entitlements = Entitlement::findInString($entitlementString)->toArray();

        $this->assertEquals($expected, $entitlements);
    }
}
