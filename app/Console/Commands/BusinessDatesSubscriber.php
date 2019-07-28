<?php

namespace App\Console\Commands;

use App\Tools\BusinessDatesCalcInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Superbalist\LaravelPubSub\PubSubConnectionFactory;
use Superbalist\PubSub\PubSubAdapterInterface;

class BusinessDatesSubscriber extends Command
{
    /**
     * The name and signature of the subscriber command.
     *
     * @var string
     */
    protected $signature = 'BankWireDaemon';

    /**
     * The subscriber description.
     *
     * @var string
     */
    protected $description = 'PubSub subscriber for Business Dates Calculator';

    /**
     * @var PubSubAdapterInterface
     */
    protected $subscriber;
    protected $publisher;

    /**
     * @var PubSubConnectionFactory
     */
    private $pubsubsFactory;

    /**
     * @var BusinessDatesCalcInterface
     */
    private $businessDatesCalc;

    /**
     * Create a new command instance.
     *
     * @param PubSubConnectionFactory $pubsubsFactory
     * @param BusinessDatesCalcInterface $businessDatesCalc
     */
    public function __construct(
        PubSubConnectionFactory $pubsubsFactory,
        BusinessDatesCalcInterface $businessDatesCalc
    ) {
        parent::__construct();
        $connectionName = config('pubsub.default');
        $connection = config("pubsub.connections.$connectionName");
        $this->subscriber = $pubsubsFactory->make($connection['driver'], $connection);
        $this->publisher = $pubsubsFactory->make($connection['driver'], $connection);
        $this->pubsubsFactory = $pubsubsFactory;
        $this->businessDatesCalc = $businessDatesCalc;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->subscriber->subscribe('BankWire:businessDates', function ($message) {
            if (is_array($message) && !empty($message['initialDate']) && !empty($message['delay'])) {
                $date = $message['initialDate'];
                $delay = $message['delay'];
                try {
                    $results = $this->businessDatesCalc->calculateBusinessDate(new \DateTime($date), $delay);
                    $response = $this->businessDatesCalc->prepareResponse(true, $message, $results);
                    Log::debug($response);
                } catch (\Exception $e) {
                    //@todo: throw and catch more specific exceptions to have a better clue on what's happening
                    $response = $this->businessDatesCalc->prepareResponse(false, $message, null, "An error occured");
                    Log::error(array_merge($response, ['exception' => $e->getCode() . ':' . $e->getMessage()]));
                }
                $this->publisher->publish('BankWire:businessDates', $response);
            }
        });
    }
}
