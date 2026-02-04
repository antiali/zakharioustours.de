<?php

namespace FlyingPress;

use ActionScheduler;

class Queue
{
  private $group_name;
  private $callback_action;
  private $ajax_action;

  public static function init()
  {
    // Remove the tasks after they are executed
    add_filter('action_scheduler_retention_period', '__return_zero');
  }

  public function __construct($group_name, $callback_action)
  {
    $this->group_name = $group_name;
    $this->callback_action = $callback_action;
    $this->ajax_action = 'flying_press_run_queue';

    add_action('wp_ajax_' . $this->ajax_action, function () {
      ActionScheduler::runner()->run($this->group_name);
      wp_die('OK');
    });
    add_action('wp_ajax_nopriv_' . $this->ajax_action, function () {
      ActionScheduler::runner()->run($this->group_name);
      wp_die('OK');
    });

    // Increase batch size to 100
    add_filter('action_scheduler_queue_runner_batch_size', fn() => 100);
  }

  public function add_task($task_data, $priority = 20)
  {
    global $wpdb;

    // Query action with the task_data and priority
    $store = ActionScheduler::store();
    $action_id = $store->query_action([
      'group' => $this->group_name,
      'status' => \ActionScheduler_Store::STATUS_PENDING,
      'args' => $task_data,
      'priority' => $priority,
    ]);

    // If the action exists, update the priority
    if ($action_id) {
      $wpdb->update(
        $wpdb->actionscheduler_actions,
        ['priority' => $priority],
        ['action_id' => $action_id]
      );

      return $action_id;
    }

    return as_enqueue_async_action(
      $this->callback_action,
      $task_data,
      $this->group_name,
      false,
      $priority
    );
  }

  public function start_queue()
  {
    $url = add_query_arg('action', $this->ajax_action, admin_url('admin-ajax.php'));
    wp_remote_get($url, [
      'timeout' => 0.01,
      'blocking' => false,
      'sslverify' => false,
    ]);
  }

  public function get_pending_count()
  {
    $store = ActionScheduler::store();
    $query_args = [
      'group' => $this->group_name,
      'status' => \ActionScheduler_Store::STATUS_PENDING,
    ];

    return $store->query_actions($query_args, 'count');
  }

  public function clear_queue()
  {
    as_unschedule_all_actions('', [], $this->group_name);
  }
}
