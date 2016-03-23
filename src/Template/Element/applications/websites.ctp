<h2>Websites</h2>

<?= $this->Form->input('website', [
    'label' => 'Official Website',
    'placeholder' => 'http://'
]); ?>
<p class="footnote">
    Please enter the full URL (e.g. http://MyBandName.com)
    or leave this blank if your band does not have its own website.
</p>

<?= $this->Form->input('social_networking', [
    'label' => 'Social Networking Sites',
    'placeholder' => 'http://',
    'type' => 'textarea'
]); ?>
<p class="footnote">
    Facebook, Bandcamp, SoundCloud, ReverbNation, Twitter, etc.
    One full URL (e.g. http://facebook.com/mybandname) per line.
</p>
