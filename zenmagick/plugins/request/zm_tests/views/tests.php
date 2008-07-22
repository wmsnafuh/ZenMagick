<h1>ZenMagick Unit Testing</h1>

<h2>Select from the following test cases to run</h2>
<?php $form->open(null, '', false, array('method'=>'get')); ?>
    <fieldset>
        <legend>Available Tests</legend>
        <?php foreach ($all_tests as $case) { ?>
            <input type="checkbox" name="tests[]" id="<?php echo $case ?>" value="<?php echo $case ?>">
            <label for="<?php echo $case ?>"><?php echo $case ?></label<br>
        <?php } ?>
    </fieldset>
    <p><input type="submit" value="Run"></p>
</form>

<?php
    if (isset($test_suite)) {
        $test_suite->run(new HtmlReporter());
    }
?>
