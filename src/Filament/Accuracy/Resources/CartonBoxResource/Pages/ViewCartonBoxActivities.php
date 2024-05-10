<?php

namespace Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\CartonBoxResource\Pages;

use Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\CartonBoxResource;
use Filament\Resources\Pages\Page;
use JaOcero\ActivityTimeline\Pages\ActivityTimelinePage;

class ViewCartonBoxActivities extends ActivityTimelinePage
{
    protected static string $resource = CartonBoxResource::class;

    //protected static string $view = 'filament.accuracy.resources.carton-box-resource.pages.view-carton-box-activities';
    protected function configuration(): array
    {
        return [
            'activity_section' => [
                'label' => 'Activities', // label for the section
                'description' => 'These are the activities that have been recorded.', // description for the section
                'show_items_count' => 0, // show the number of items to be shown
                'show_items_label' => 'Show more', // show button label
                'show_items_icon' => 'heroicon-o-chevron-down', // show button icon,
                'show_items_color' => 'gray', // show button color,
                'aside' => true, // show the section in the aside
                'empty_state_heading' => 'No activities yet', // heading for the empty state
                'empty_state_description' => 'Check back later for activities that have been recorded.', // description for the empty state
                'empty_state_icon' => 'heroicon-o-bolt-slash', // icon for the empty state
                'heading_visible' => true, // show the heading
                'extra_attributes' => [], // extra attributes
            ],
            'activity_title' => [
                'placeholder' => 'No title is set', // this will show when there is no title
                'allow_html' => true, // set true to allow html in the title

                /**
             * You are free to adjust the state before displaying it on your page.
             * Take note that the state returns these data below:
             *      [
             *       'log_name' => $activity->log_name,
             *      'description' => $activity->description,
             *      'subject' => $activity->subject,
             *      'event' => $activity->event,
             *      'causer' => $activity->causer,
             *      'properties' => json_decode($activity->properties, true),
             *      'batch_uuid' => $activity->batch_uuid,
             *     ]

             * If you wish to make modifications, please refer to the default code in the HasSetting trait.
             */

                // 'modify_state' => function (array $state) {
                //
                // }

            ],
            'activity_description' => [
                'placeholder' => 'No description is set', // this will show when there is no description
                'allow_html' => true, // set true to allow html in the description


                /**
             * You are free to adjust the state before displaying it on your page.
             * Take note that the state returns these data below:
             *      [
             *       'log_name' => $activity->log_name,
             *      'description' => $activity->description,
             *      'subject' => $activity->subject,
             *      'event' => $activity->event,
             *      'causer' => $activity->causer,
             *      'properties' => json_decode($activity->properties, true),
             *      'batch_uuid' => $activity->batch_uuid,
             *     ]

             * If you wish to make modifications, please refer to the default code in the HasSetting trait.
             */

                // 'modify_state' => function (array $state) {
                //
                // }

            ],
            'activity_date' => [
                'name' => 'created_at', // or updated_at
                'date' => 'F j, Y g:i A', // date format
                'placeholder' => 'No date is set', // this will show when there is no date
            ],
            'activity_icon' => [
                'icon' => fn (string | null $state): string | null => match ($state) {
                        /**
                     * 'event_name' => 'heroicon-o-calendar',
                     * ... and more
                     */
                    default => 'heroicon-o-pencil-square'
                },
                'color' => fn (string | null $state): string | null => match ($state) {
                        /**
                     * 'event_name' => 'primary',
                     * ... and more
                     */
                    default => 'primary'
                },
            ]
        ];
    }
}
