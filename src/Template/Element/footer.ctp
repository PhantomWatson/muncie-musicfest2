<?php
    $sponsors = [
        [
            'logo' => 'first-merchants.png',
            'name' => 'First Merchants Bank',
            'url' => 'https://www.firstmerchants.com/'
        ],
        [
            'logo' => 'first-merchants-pwa.jpg',
            'name' => 'First Merchants Private Wealth Advisors',
            'url' => 'https://www.firstmerchants.com/first-merchants-private-wealth-advisors/'
        ],
        [
            'logo' => 'cornerstone.small.png',
            'name' => 'Cornerstone Center for the Arts',
            'url' => 'http://cornerstonearts.org/'
        ],
        [
            'logo' => 'macc-logo-200px.jpg',
            'name' => 'Muncie Arts and Culture Council',
            'url' => 'http://munciearts.org/'
        ],
        [
            'logo' => 'ballbrothers.small.jpg',
            'name' => 'Ball Brothers Foundation',
            'url' => 'https://www.ballfdn.org/'
        ],
        [
            'logo' => 'george-and-frances-ball-foundation.png',
            'name' => 'George and Frances Ball Foundation',
            'url' => 'http://www.gfballfoundation.org'
        ],
        [
            'logo' => 'walmart-foundation.png',
            'name' => 'Walmart Foundation',
            'url' => 'http://giving.walmart.com/foundation'
        ],
        [
            'logo' => 'max.jpg',
            'name' => 'MAX (93.5 FM)',
            'url' => 'http://www.maxrocks.net/'
        ],
        [
            'logo' => 'werk.jpg',
            'name' => 'WERK (104.9 FM)',
            'url' => 'http://werkfm.net/'
        ],
        [
            'logo' => 'wlbc.jpg',
            'name' => 'WLBC (104.1 FM)',
            'url' => 'http://www.wlbc.com/'
        ],
        [
            'logo' => 'sightandsound.png',
            'name' => 'Sight and Sound',
            'url' => 'https://www.sightandsoundmusic.com/'
        ],
        [
            'logo' => 'comedy-underground.jpg',
            'name' => 'Comedy Underground',
            'url' => 'https://www.facebook.com/ComedyUndergroundMuncie/'
        ],
        [
            'logo' => 'be-here-now.png',
            'name' => 'Be Here Now',
            'url' => 'http://beherenowmusic.com/'
        ],
        [
            'logo' => 'muncie-edmc.png',
            'name' => 'Muncie EDMC',
            'url' => 'https://www.facebook.com/muncieedmc/'
        ],
        [
            'logo' => 'greeks-pizzeria.png',
            'name' => 'Greek\'s Pizzeria',
            'url' => 'http://www.greekspizzeria.com/'
        ],
    ];
    $perRow = 4;
?>

<div id="sponsors">
    <h2>
        2016 Sponsors and Supporters
    </h2>
    <div class="row">
        <?php
            // The particular way that vertical alignment is accomplished here requires no whitespace between divs
            foreach ($sponsors as $i => $sponsor) {
                echo '<div class="col-sm-' . (12 / $perRow) . '">';
                if ($sponsor['logo']) {
                    echo '<a href= "' . $sponsor['url'] . '">';
                    echo '<img src= "/img/sponsors/' . $sponsor['logo'] . '" title= "' . $sponsor['name'] . '" alt = "' . $sponsor['name'] . '" />';
                    echo '</a >';
                } else {
                    echo '<a href="' . $sponsor['url'] . '">' . $sponsor['name'] . '</a>';
                }
                echo '</div>';
                if (($i + 1) % $perRow == 0) {
                    echo '</div><div class="row">';
                }
            }
        ?>
    </div>
</div>

All rights reserved &copy; <?php echo date('Y'); ?> Muncie MusicFest
<br />
Problems with this page?
<a href="mailto:info@munciemusicfest.com?subject=Muncie MusicFest: Problem with <?= $this->request->here ?>">
    Email us
</a>
