<?php $schedule = $currentOrder->getSchedule(); ?>
<?php if (!ZMLangUtils::isEmpty($schedule)) { ?>
    <h3><?php zm_l10n("Subscription") ?></h3>
    <?php $schedules = $subscriptions->getSchedules(); ?>
    <p><?php zm_l10n("Order schedule: '%s.'", $schedules[$schedule]['name']) ?></p>
<?php } ?>