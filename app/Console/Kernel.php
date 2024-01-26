<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Campaigns;
use App\Models\Contacts;
use App\Models\TrashContacts;
use App\Jobs\createCampaignEmailQueue;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //schedule contacts cleanup
        $schedule->call(function (){
            $contacts = Contacts::where('attemps', '>=', 3)->where('point', '<', 0)->get();
            foreach ($contacts as $contact) {
                TrashContacts::create($contact->toArray());
                $contact->delete();
            }
        })->weekly();

        $schedule->call(function () {
            $campaigns = Campaigns::where("status", "publish")->where("schedule", "<=", now())->get();

            foreach ($campaigns as $campaign) {
                createCampaignEmailQueue::dispatch($campaign->id);
            }
        })
            ->everyMinute();

        $campaigns = Campaigns::where("status", "completed")->where('type', 'repeat')->get();

        foreach ($campaigns as $campaign) {


            $campaignDateTime = new \DateTime($campaign->schedule);
            $timeString = $campaignDateTime->format('H:i');

            switch ($campaign->repeat) {


                /**
                 * =======================================================================
                 * Every 5 minutes
                 * =======================================================================
                 */


                case "every5minutes":
                    
                    $schedule->call(function () use ($campaign) {
                        createCampaignEmailQueue::dispatch($campaign->id);
                    })
                    ->everyFiveMinutes();
                    break;

                /**
                 * =======================================================================
                 * Everyday
                 * =======================================================================
                 */

                case "everyday":
                    
                    $schedule->call(function () use ($campaign) {
                        createCampaignEmailQueue::dispatch($campaign->id);
                    })
                    ->dailyAt($timeString);
                    break;

                /**
                 * =======================================================================
                 * Every Week
                 * =======================================================================
                 */

                case 'everyweek':
                    
                    // Get the day of the week as a numeric representation (1 for Monday, 2 for Tuesday, etc.)

                    $dayOfWeekNumeric = $campaignDateTime->format('N');

                    // Convert the numeric representation to the corresponding Laravel scheduler method

                    $dayOfWeekMethod = [
                        1 => 'mondays',
                        2 => 'tuesdays',
                        3 => 'wednesdays',
                        4 => 'thursdays',
                        5 => 'fridays',
                        6 => 'saturdays',
                        7 => 'sundays',
                    ][$dayOfWeekNumeric];

                    // Schedule the task based on the determined day of the week
                    $schedule->call(function () use ($campaign) {
                        createCampaignEmailQueue::dispatch($campaign->id);
                    })
                    ->$dayOfWeekMethod()
                    ->dailyAt($timeString);
                    break;

                /**
                 * =======================================================================
                 * Every 15 days
                 * =======================================================================
                 */

                case 'every15days':
                    $schedule->call(function () use ($campaign) {
                        createCampaignEmailQueue::dispatch($campaign->id);
                    })->dailyAt($timeString);
            
                    break;

                /**
                 * =======================================================================
                 * Every month
                 * =======================================================================
                 */
            
                case 'everymonth':
                    $schedule->call(function () use ($campaign) {
                        createCampaignEmailQueue::dispatch($campaign->id);
                    })->monthlyOn($campaignDateTime->format('d'), $timeString);
            
                    break;

                /**
                 * =======================================================================
                 * Every 3 months
                 * =======================================================================
                 */
            
                case 'every3months':
                    $schedule->call(function () use ($campaign) {
                        createCampaignEmailQueue::dispatch($campaign->id);
                    })->everyThreeMonths($timeString);
            
                    break;


                /**
                 * =======================================================================
                 * Every 6 months
                 * =======================================================================
                 */
            
                case 'every6months':
                    $schedule->call(function () use ($campaign) {
                        createCampaignEmailQueue::dispatch($campaign->id);
                    })->everySixMonths($timeString);
            
                    break;


                /**
                 * =======================================================================
                 * Every year
                 * =======================================================================
                 */
            
                case 'everyyear':
                    $schedule->call(function () use ($campaign) {
                        createCampaignEmailQueue::dispatch($campaign->id);
                    })->yearly()->monthlyOn($campaignDateTime->format('d'), $timeString);
            
                    break;
            
                default:
                    // Handle other cases or provide a default behavior
                    break;
                
            }
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
