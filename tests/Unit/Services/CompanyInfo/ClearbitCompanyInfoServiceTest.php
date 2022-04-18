<?php

namespace Tests\Unit\Services\CompanyInfo;

use App\Services\CompanyInfo\ClearbitCompanyInfoService;
use Tests\TestCase;

class ClearbitCompanyInfoServiceTest extends TestCase
{
    /** @test */
    public function it_can_get_data_and_structure_is_correct()
    {
        $service = new ClearbitCompanyInfoService();
        $result = $service->getResults("facebook.com");

        $this->assertObjectHasAttribute('name', $result);
        $this->assertNotNull($result->name);

        $this->assertObjectHasAttribute('legalName', $result);
        $this->assertNotNull($result->legalName);

        $this->assertObjectHasAttribute('description', $result);
        $this->assertNotNull($result->description);

        $this->assertObjectHasAttribute('location', $result);
        $this->assertNotNull($result->location);

        $this->assertObjectHasAttribute('logo', $result);
        $this->assertNotNull($result->logo);
    }
}
