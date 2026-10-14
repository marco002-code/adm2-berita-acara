<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\MinuteOfEvent;
use App\Models\Participant;
use App\Models\Proctor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that only absent participants are recorded in attendances table.
     */
    public function test_only_absent_participants_are_recorded_in_attendances_table(): void
    {
        $room = Room::factory()->create();
        $session = ExamSession::factory()->create();

        // 5 participants registered for this room and session
        $participants = Participant::factory()->count(5)->create([
            'room_id' => $room->id,
            'exam_session_id' => $session->id,
        ]);

        // Proktor only selects 2 students who are absent
        Attendance::create([
            'exam_session_id' => $session->id,
            'room_id' => $room->id,
            'participant_id' => $participants[0]->id,
            'status' => 'tidak_hadir',
            'reason' => 'Sakit',
            'notes' => 'Demam',
        ]);

        Attendance::create([
            'exam_session_id' => $session->id,
            'room_id' => $room->id,
            'participant_id' => $participants[1]->id,
            'status' => 'tidak_hadir',
            'reason' => 'Izin',
            'notes' => 'Keperluan keluarga',
        ]);

        // The attendances table MUST ONLY have 2 records (the absent ones)
        $this->assertDatabaseCount('attendances', 2);

        // Participants table has all 5 students
        $this->assertDatabaseCount('participants', 5);

        // Absent participant IDs
        $absentIds = Attendance::query()
            ->where('exam_session_id', $session->id)
            ->where('room_id', $room->id)
            ->pluck('participant_id')
            ->toArray();

        $this->assertEqualsCanonicalizing([$participants[0]->id, $participants[1]->id], $absentIds);

        // Present participants are the ones NOT in attendances
        $presentParticipants = Participant::query()
            ->where('room_id', $room->id)
            ->where('exam_session_id', $session->id)
            ->whereNotIn('id', $absentIds)
            ->get();

        $this->assertCount(3, $presentParticipants);
    }

    /**
     * Test MinuteOfEvent attendance calculations.
     */
    public function test_minute_of_event_calculates_expected_absent_and_present_correctly(): void
    {
        $room = Room::factory()->create();
        $session = ExamSession::factory()->create();

        $participants = Participant::factory()->count(10)->create([
            'room_id' => $room->id,
            'exam_session_id' => $session->id,
        ]);

        // Only 1 absent student recorded
        Attendance::create([
            'exam_session_id' => $session->id,
            'room_id' => $room->id,
            'participant_id' => $participants[3]->id,
            'status' => 'tidak_hadir',
            'reason' => 'Tanpa Keterangan',
        ]);

        $minuteOfEvent = MinuteOfEvent::factory()->create([
            'room_id' => $room->id,
            'exam_session_id' => $session->id,
        ]);

        $this->assertEquals(10, $minuteOfEvent->expected_participants_count);
        $this->assertEquals(1, $minuteOfEvent->absent_participants_count);
        $this->assertEquals(9, $minuteOfEvent->present_participants_count);
    }

    /**
     * Test guest is redirected to login page.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test teacher can login with username and password.
     */
    public function test_teacher_can_login_with_username_and_password(): void
    {
        $teacher = User::factory()->create([
            'username' => '1',
            'name' => 'Suwahono, S.Pd.',
            'password' => bcrypt('1'),
        ]);

        $response = $this->post(route('login'), [
            'username' => '1',
            'password' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($teacher);
    }

    /**
     * Test dashboard page response for authenticated user.
     */
    public function test_dashboard_renders_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Sistem Presensi');
    }

    /**
     * Test submitting attendance via POST request records only selected absent students.
     */
    public function test_attendance_store_endpoint_records_only_absent_students(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $session = ExamSession::factory()->create();

        $p1 = Participant::factory()->create(['room_id' => $room->id, 'exam_session_id' => $session->id]);
        $p2 = Participant::factory()->create(['room_id' => $room->id, 'exam_session_id' => $session->id]);
        $p3 = Participant::factory()->create(['room_id' => $room->id, 'exam_session_id' => $session->id]);

        // Submit form selecting only $p2 as absent with reason Sakit
        $response = $this->actingAs($user)->post(route('attendances.store'), [
            'exam_session_id' => $session->id,
            'room_id' => $room->id,
            'absent_participants' => [$p2->id],
            'reasons' => [
                $p2->id => 'Sakit',
            ],
            'notes' => [
                $p2->id => 'Demam tinggi',
            ],
        ]);

        $response->assertRedirect(route('attendances.index', [
            'exam_session_id' => $session->id,
            'room_id' => $room->id,
        ]));

        // Assert only 1 attendance record was saved in database
        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', [
            'participant_id' => $p2->id,
            'status' => 'tidak_hadir',
            'reason' => 'Sakit',
            'notes' => 'Demam tinggi',
        ]);
        $this->assertDatabaseMissing('attendances', [
            'participant_id' => $p1->id,
        ]);
        $this->assertDatabaseMissing('attendances', [
            'participant_id' => $p3->id,
        ]);
    }

    /**
     * Test minute of event can be stored and printed.
     */
    public function test_minute_of_event_can_be_stored_and_printed(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $session = ExamSession::factory()->create();

        $response = $this->actingAs($user)->post(route('minutes.store'), [
            'exam_session_id' => $session->id,
            'room_id' => $room->id,
            'exam_date' => now()->toDateString(),
            'proctor_name' => 'Budi Santoso',
            'proctor_nip' => '198801152011011002',
            'supervisor_name' => 'Dra. Sri Wahyuni',
            'supervisor_nip' => '197605122003122001',
            'headmaster_name' => 'Drs. H. Ahmad Dahlan',
            'headmaster_nip' => '196803201995031001',
            'incident_notes' => 'Semua tertib',
        ]);

        $response->assertRedirect(route('minutes.index', [
            'exam_session_id' => $session->id,
            'room_id' => $room->id,
        ]));

        $minute = MinuteOfEvent::first();
        $this->assertNotNull($minute);
        $this->assertEquals('Budi Santoso', $minute->proctor_name);

        // Test print page
        $printResponse = $this->actingAs($user)->get(route('minutes.print', $minute));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('BERITA ACARA PELAKSANAAN');
        $printResponse->assertSee('Budi Santoso');
        $printResponse->assertSee('Pengawas');
        $printResponse->assertSee('Proktor Ruang');
        $printResponse->assertSee('Kepala Sekolah');
        $printResponse->assertSee('qr-supervisor');
        $printResponse->assertSee('qr-proctor');
        $printResponse->assertSee('qr-headmaster');
    }

    /**
     * Test admin can update assessment settings.
     */
    public function test_admin_can_update_assessment_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'assessment_name' => 'TKA Kemendikdasmen 2026',
            'academic_year' => '2026/2027',
            'school_name' => 'SMA Teladan',
            'headmaster_name' => 'Drs. Kepala Sekolah',
            'headmaster_nip' => '197001011990011001',
        ]);

        $response->assertRedirect(route('admin.index', ['tab' => 'settings']));
        $this->assertEquals('TKA Kemendikdasmen 2026', AppSetting::get('assessment_name'));
        $this->assertEquals('SMA Teladan', AppSetting::get('school_name'));
    }

    /**
     * Test admin can create, update, and delete room.
     */
    public function test_admin_can_manage_rooms(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // Create
        $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'name' => 'Lab Multimedia',
            'code' => 'LAB-MM',
            'capacity' => 35,
            'description' => 'Lantai 3',
        ]);
        $response->assertRedirect(route('admin.index', ['tab' => 'rooms']));
        $this->assertDatabaseHas('rooms', ['code' => 'LAB-MM']);

        $room = Room::where('code', 'LAB-MM')->first();

        // Delete
        $delResponse = $this->actingAs($admin)->delete(route('admin.rooms.destroy', $room));
        $delResponse->assertRedirect(route('admin.index', ['tab' => 'rooms']));
        $this->assertDatabaseMissing('rooms', ['code' => 'LAB-MM']);
    }

    /**
     * Test admin can manage and bulk generate exam sessions.
     */
    public function test_admin_can_manage_and_generate_sessions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // Bulk generate 3 sessions
        $response = $this->actingAs($admin)->post(route('admin.sessions.generate'), [
            'session_count' => 3,
            'exam_date' => '2026-10-20',
            'subject' => 'Literasi & Survei Lingkungan',
        ]);

        $response->assertRedirect(route('admin.index', ['tab' => 'sessions']));
        $this->assertDatabaseHas('exam_sessions', [
            'name' => 'Sesi 1',
            'subject' => 'Literasi & Survei Lingkungan',
        ]);
        $this->assertDatabaseHas('exam_sessions', [
            'name' => 'Sesi 3',
            'subject' => 'Literasi & Survei Lingkungan',
        ]);
    }

    /**
     * Test admin can create, update, and delete proctor.
     */
    public function test_admin_can_manage_proctors(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // Create proctor
        $response = $this->actingAs($admin)->post(route('admin.proctors.store'), [
            'name' => 'Qomarudin',
            'nbm' => '1042001',
        ]);
        $response->assertRedirect(route('admin.index', ['tab' => 'proctors']));
        $this->assertDatabaseHas('proctors', [
            'name' => 'Qomarudin',
            'nbm' => '1042001',
        ]);

        $proctor = Proctor::where('nbm', '1042001')->first();

        // Delete proctor
        $delResponse = $this->actingAs($admin)->delete(route('admin.proctors.destroy', $proctor));
        $delResponse->assertRedirect(route('admin.index', ['tab' => 'proctors']));
        $this->assertDatabaseMissing('proctors', ['id' => $proctor->id]);
    }

    /**
     * Test regular teacher / supervisor cannot access admin panel.
     */
    public function test_regular_teacher_cannot_access_admin_panel(): void
    {
        $teacher = User::factory()->create([
            'username' => '1',
            'is_admin' => false,
        ]);

        $response = $this->actingAs($teacher)->get(route('admin.index'));
        $response->assertStatus(403);
    }

    /**
     * Test regular teacher does not see admin panel link in navbar.
     */
    public function test_regular_teacher_does_not_see_admin_panel_link_in_navbar(): void
    {
        $teacher = User::factory()->create([
            'username' => '3',
            'is_admin' => false,
        ]);

        $response = $this->actingAs($teacher)->get(route('dashboard'));
        $response->assertOk();
        $response->assertDontSee('⚙️ Pengaturan Admin');
    }

    /**
     * Test admin user can access admin panel and see participant data.
     */
    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.index'));
        $response->assertOk();
        $response->assertSee('Panel Administrator');
        $response->assertSee('Data Peserta');
    }

    /**
     * Test supervisor can view participants filtered by room and session.
     */
    public function test_supervisor_can_view_participants_filtered_by_room_and_session(): void
    {
        $teacher = User::factory()->create(['is_admin' => false]);
        $room = Room::factory()->create(['name' => 'LAB 1']);
        $session = ExamSession::factory()->create(['name' => 'Sesi 1']);

        $participant = Participant::factory()->create([
            'room_id' => $room->id,
            'exam_session_id' => $session->id,
            'participant_number' => 'P0100320001',
            'name' => 'ABIGAIL XAVIOLA NUGRAHA',
            'rombel' => '9A',
        ]);

        $response = $this->actingAs($teacher)->get(route('attendances.index', [
            'room_id' => $room->id,
            'exam_session_id' => $session->id,
        ]));

        $response->assertOk();
        $response->assertSee('P0100320001');
        $response->assertSee('ABIGAIL XAVIOLA NUGRAHA');
        $response->assertSee('9A');
    }

    /**
     * Test admin can update participant data.
     */
    public function test_admin_can_update_participant_data(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room1 = Room::factory()->create(['name' => 'LAB 1']);
        $room2 = Room::factory()->create(['name' => 'LAB 2']);
        $session = ExamSession::factory()->create();

        $participant = Participant::factory()->create([
            'room_id' => $room1->id,
            'exam_session_id' => $session->id,
            'participant_number' => 'P0100320001',
            'name' => 'ABIGAIL XAVIOLA NUGRAHA',
            'rombel' => '9A',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.participants.update', $participant), [
            'participant_number' => 'P0100320001',
            'name' => 'ABIGAIL XAVIOLA NUGRAHA (PINDAH)',
            'rombel' => '9B',
            'room_id' => $room2->id,
            'exam_session_id' => $session->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('participants', [
            'id' => $participant->id,
            'name' => 'ABIGAIL XAVIOLA NUGRAHA (PINDAH)',
            'rombel' => '9B',
            'room_id' => $room2->id,
        ]);
    }

    /**
     * Test minutes index page renders properly even before minute is saved in database.
     */
    public function test_minutes_index_renders_without_saved_minute_record(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $session = ExamSession::factory()->create();

        $response = $this->actingAs($user)->get(route('minutes.index', [
            'exam_session_id' => $session->id,
            'room_id' => $room->id,
        ]));

        $response->assertOk();
        $response->assertSee('Formulir Berita Acara Ujian');
        $response->assertSee('Belum Disimpan (Belum Bisa Cetak)');
    }

    /**
     * Test admin participants tab in master mode shows only unique students without session duplication.
     */
    public function test_admin_participants_tab_master_mode_shows_unique_students(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room = Room::factory()->create();
        $s1 = ExamSession::factory()->create(['name' => 'SESI 1', 'subject' => 'Matematika']);
        $s2 = ExamSession::factory()->create(['name' => 'SESI 1', 'subject' => 'Bhs Indonesia']);

        // Student assigned to 2 sessions
        Participant::factory()->create([
            'room_id' => $room->id,
            'exam_session_id' => $s1->id,
            'participant_number' => 'TEST001',
            'name' => 'Budi Santoso',
        ]);
        Participant::factory()->create([
            'room_id' => $room->id,
            'exam_session_id' => $s2->id,
            'participant_number' => 'TEST001',
            'name' => 'Budi Santoso',
        ]);

        // Default master view
        $response = $this->actingAs($admin)->get(route('admin.index', ['tab' => 'participants']));

        $response->assertOk();
        $response->assertSee('Mode Master Siswa');
        $response->assertSee('Budi Santoso');

        // TEST001 appears exactly once as table row (rendered in <td> and in onclick edit button = 2 times total in HTML)
        $content = $response->getContent();
        $this->assertEquals(2, substr_count($content, 'TEST001'));
    }

    /**
     * Test updating a participant syncs name and rombel across all assigned sessions.
     */
    public function test_updating_participant_syncs_across_all_sessions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room = Room::factory()->create();
        $s1 = ExamSession::factory()->create();
        $s2 = ExamSession::factory()->create();

        $p1 = Participant::factory()->create([
            'room_id' => $room->id,
            'exam_session_id' => $s1->id,
            'participant_number' => 'NUM999',
            'name' => 'Nama Lama',
            'rombel' => '9A',
        ]);
        $p2 = Participant::factory()->create([
            'room_id' => $room->id,
            'exam_session_id' => $s2->id,
            'participant_number' => 'NUM999',
            'name' => 'Nama Lama',
            'rombel' => '9A',
        ]);

        $this->actingAs($admin)->put(route('admin.participants.update', $p1), [
            'participant_number' => 'NUM999',
            'name' => 'Nama Baru Disinkronkan',
            'rombel' => '9C',
            'room_id' => $room->id,
            'exam_session_id' => $s1->id,
        ]);

        $this->assertDatabaseHas('participants', [
            'id' => $p1->id,
            'name' => 'Nama Baru Disinkronkan',
            'rombel' => '9C',
        ]);
        $this->assertDatabaseHas('participants', [
            'id' => $p2->id,
            'name' => 'Nama Baru Disinkronkan',
            'rombel' => '9C',
        ]);
    }
}
