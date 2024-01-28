<div class="macf">
	<div class="macf-header">
        <h1>Multisite ACF Settings</h1>

        <p>Copy ACF values from one post of one site to another.</p>
    </div>
    <div class="macf-wrapper">
        <form method="post" action="" class="macf-form">
            <div class="macf-box">
                <div class="macf-box__header">
                    <h2>Select source site</h2>
                </div>
                <div class="macf-box__content">
                    <div class="macf-box__group">
                        <label for="source_site">Source Site:</label>
                        <select name="source_site" id="source_site" required>
                            <option value="">Select multisite...</option>
						    <?php
						    $sites = get_sites();
						    foreach ($sites as $site) {
							    $blog_id = $site->blog_id;
							    $blog_name = get_blog_details($blog_id)->blogname;
							    echo "<option value='$blog_id'>$blog_name</option>";
						    }
						    ?>
                        </select>
                    </div>
                    <div class="macf-box__group" style="display: none">
                        <label for="source_page">Source Page:</label>
                        <select name="source_page" id="source_page" required>
                            <option value="">Select page...</option>
                        </select>
                    </div>
                    <div class="macf-box__group" style="display: none" id="source_acf">

                    </div>
                </div>
            </div>

            <div class="macf-box">
                <div class="macf-box__header">
                    <h2>Select destination site</h2>
                </div>
                <div class="macf-box__content">
                    <div class="macf-box__group">
                        <label for="destination_site">Source Site:</label>
                        <select name="destination_site" id="destination_site" required>
                            <option value="">Select multisite...</option>
				            <?php
				            $sites = get_sites();
				            foreach ($sites as $site) {
					            $blog_id = $site->blog_id;
					            $blog_name = get_blog_details($blog_id)->blogname;
					            echo "<option value='$blog_id'>$blog_name</option>";
				            }
				            ?>
                        </select>
                    </div>
                    <div class="macf-box__group" style="display: none">
                        <label for="destination_page">Source Page:</label>
                        <select name="destination_page" id="destination_page" required>
                            <option value="">Select page...</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="macf-form__check ">
                <input type="checkbox" name="macf_check" id="macf_check" required>
                <label for="macf_check">I confirm that I really want to transfer this custom fields</label>
            </div>
            <div class="macf-form__submit">
                <button type="submit" name="submit">
                    Submit
                </button>
            </div>
        </form>
    </div>

	<?php

	wp_enqueue_style('multisite-acf-select2', MULTISITE_ACF_PLUGIN_URL . 'assets/select2/nice-select2.css', array(), '');
	wp_enqueue_script('multisite-acf-select2', MULTISITE_ACF_PLUGIN_URL . 'assets/select2/nice-select2.js', '', '', true);
	?>

    <script>
        window.addEventListener('load', function() {
            new MACF(document.querySelector('.macf'));
            const selects = [
                'source_site',
                'source_page',
                'destination_page',
                'destination_site'
            ];
            /*selects.forEach((element) =>
                NiceSelect.bind(document.getElementById(element),{searchable: true})
            );
*/
        });

    </script>
</div>
