<!-- Required Js -->
<script src="/build/js/plugins/popper.min.js"></script>
<script src="/build/js/plugins/simplebar.min.js"></script>
<script src="/build/js/plugins/bootstrap.min.js"></script>
<script src="/build/js/fonts/custom-font.js"></script>
<script src="/build/js/pcoded.js"></script>
<script src="/build/js/plugins/feather.min.js"></script>

@if (config('app.dark_layout') != "")
<script>
  layout_change("{{config('app.dark_layout')}}");
</script>
@endif
@if (config('app.theme_contrast') != '')
<script>
  layout_theme_contrast_change("{{config('app.theme_contrast')}}");
</script>
@endif
@if (config('app.box_container') != '')
<script>
  change_box_container("{{config('app.box_container')}}");
</script>
@endif
@if (config('app.caption_show') != '')
<script>
  layout_caption_change("{{config('app.caption_show')}}");
</script>
@endif
@if (config('app.rtl_layout') != '')
<script>
  layout_rtl_change("{{config('app.rtl_layout')}}");
</script>
@endif
@if (config('app.preset_theme') != "")
<script>
  preset_change("{{config('app.preset_theme')}}");
</script>
@endif
@if (config('app.theme_layout') == 'default')
<script>
  main_layout_change('default');
</script>
@elseif(config('app.theme_layout') != "")
<script>
  main_layout_change('dark');
</script>
@endif