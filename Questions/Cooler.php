<h3>CPU Cooler</h3>
<p>
    Do you require a CPU Cooler?<br />
    (NOTE: If the choosen CPU does not come with a stock cooler, a low end AirCooler will be selected instead)
</p>
<label for="option-one" class="pure-radio">
    <input id="option-one" type="radio" name="Cooler" value="Water" required>
    Yes WaterCooler
</label>
<label for="option-two" class="pure-radio">
    <input id="option-two" type="radio" name="Cooler" value="Air" required>
    Yes AirCooler
</label>
<?php
echo '<label for="option-two" class="pure-radio">
    <input id="option-two" type="radio" name="Cooler" value="No" required>
    No Stock cooler
</label>';
?>
