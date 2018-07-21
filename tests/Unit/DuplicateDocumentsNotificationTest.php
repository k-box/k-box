<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use KBox\DuplicateDocument;
use Illuminate\Support\Facades\Mail;
use KBox\Events\FileDuplicateFoundEvent;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Notifications\DuplicateDocumentsNotification;

class DuplicateDocumentsNotificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_notification_is_sent_and_duplicates_marked_as_sent()
    {
        Notification::fake();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $duplicate = $this->createDuplicates($user)->first();

        $event = new FileDuplicateFoundEvent($user, $duplicate);

        $notification = new DuplicateDocumentsNotification();

        $notification->handle($event);

        Notification::assertSentTo(
            $user,
            DuplicateDocumentsNotification::class,
            function ($notification, $channels) use ($duplicate) {
                return $notification->duplicateEvent->duplicateDocument->id === $duplicate->id;
            }
        );

        $this->assertTrue($duplicate->fresh()->sent, "notification not marked as sent");
    }

    public function test_notification_is_skipped_when_no_duplicates_are_left()
    {
        Notification::fake();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $duplicates = $this->createDuplicates($user, 2, ['notification_sent_at' => Carbon::now()]);

        $event = new FileDuplicateFoundEvent($user, $duplicates->first());

        $notification = new DuplicateDocumentsNotification();

        $notification->handle($event);

        Notification::assertNotSentTo(
            [$user],
            DuplicateDocumentsNotification::class
        );
    }

    public function test_notification_includes_newer_duplicates()
    {
        Notification::fake();
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $duplicates = $this->createDuplicates($user, 3, ['user_id' => $user->id]);

        $event = new FileDuplicateFoundEvent($user, $duplicates->first());

        $notification = new DuplicateDocumentsNotification();

        $notification->handle($event);

        Notification::assertSentTo(
            $user,
            DuplicateDocumentsNotification::class,
            function ($notification, $channels) use ($duplicates) {
                return $notification->duplicateEvent->duplicateDocument->id === $duplicates->first()->id;
            }
        );

        $freshDuplicates = $duplicates->map->fresh();

        $this->assertEquals([true, true, true], $freshDuplicates->pluck('sent')->toArray(), "notification not marked as sent");
    }

    public function test_notification_mail_message_lists_the_duplicates()
    {
        Notification::fake();
        Mail::fake();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $duplicates = $this->createDuplicates($user, 3, ['user_id' => $user->id]);

        $event = new FileDuplicateFoundEvent($user, $duplicates->first());

        $notification = new DuplicateDocumentsNotification();

        $notification->handle($event);

        $mail = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals($mail->markdown, "emails.duplicatenotification");
        $this->assertEquals($mail->subject, trans('mail.duplicatesnotification.subject'));
        $this->assertEquals(trans('mail.duplicatesnotification.action'), $mail->actionText);
        $this->assertEquals(route('documents.recent', ['range' => 'today']), $mail->actionUrl);
        $this->assertEquals(1, collect($mail->introLines)->count());

        $this->assertTrue(isset($mail->viewData['duplicates']), "Duplicates array not found in view");
        $this->assertCount(3, $mail->viewData['duplicates']);
    }

    private function createDuplicates($user, $count = 1, $options = [])
    {
        return factory(DuplicateDocument::class, $count)->create($options);
    }
}
