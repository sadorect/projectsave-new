<?php

namespace Tests\Unit;

use App\Services\Devotionals\ProjectsaveDevotionalImportService;
use Tests\TestCase;

class ProjectsaveDevotionalImportServiceTest extends TestCase
{
    public function test_it_parses_projectsave_devotionals_and_uses_prayer_when_action_point_is_missing(): void
    {
        $content = <<<'TEXT'
EXTRACTED PROJECTSAVE DAILY DEVOTIONALS
Matches found: 2

====================================================================================================
DEVOTIONAL 0001 | Source start line: 10
----------------------------------------------------------------------------------------------------
12/20/20, 5:45 AM - Lanre Oyeleke: ProjectSave Devotional
Sunday,
December 20th, 2020.

*THE POWER OF SHAMELESS AUDACITY IN PRAYER*

Luke 11:8 I tell you, even though he will not get up and give you the bread because of friendship.

Keep asking, keep seeking and keep knocking on the door of heaven.

*PRAYER*: Father, I receive the grace to be persistent in the place of prayer in Jesus name.

====================================================================================================
DEVOTIONAL 0002 | Source start line: 40
----------------------------------------------------------------------------------------------------
12/21/20, 5:28 AM - Lanre Oyeleke: PROJECTSAVE DEVOTIONAL
Monday,
December 21st, 2020.

*CONSIDER THIS BEFORE MARRIAGE*

Proverb 22:24 Do not make friends with a hot-tempered person.

Ability to control one's temper is a sign of maturity.

*ACTION POINT*: Run from abusive relationships.
TEXT;

        $path = tempnam(sys_get_temp_dir(), 'devotional-import-');
        file_put_contents($path, $content);

        $service = new ProjectsaveDevotionalImportService();
        $entries = $service->parseFile($path);

        @unlink($path);

        $this->assertCount(2, $entries);

        $first = $entries[0];
        $this->assertSame('THE POWER OF SHAMELESS AUDACITY IN PRAYER', $first['title']);
        $this->assertSame('Prayer', $first['category_name']);
        $this->assertStringContainsString('persistent in the place of prayer', strip_tags((string) $first['action_point']));
        $this->assertStringNotContainsString('persistent in the place of prayer', strip_tags((string) $first['details']));
        $this->assertSame('Luke 11:8', $first['scripture']);

        $second = $entries[1];
        $this->assertSame('CONSIDER THIS BEFORE MARRIAGE', $second['title']);
        $this->assertSame('Marriage & Relationships', $second['category_name']);
        $this->assertStringContainsString('Run from abusive relationships.', strip_tags((string) $second['action_point']));
    }
}
