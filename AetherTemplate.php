<?php
/**
 * Neverland - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

/**
 * QuickTemplate class for Neverland skin
 * @ingroup Skins
 */
class AetherTemplate extends BaseTemplate {

  /* Functions */

  /**
   * Outputs the entire contents of the (X)HTML page
   */
  public function execute() {

    // Build additional attributes for navigation urls
    $nav = $this->data['content_navigation'];

    if ( $this->config->get( 'AetherUseIconWatch' ) ) {
      $mode = $this->getSkin()->getUser()->isWatched( $this->getSkin()->getRelevantTitle() )
                      ? 'unwatch'
                      : 'watch';

      if ( isset( $nav['actions'][$mode] ) ) {
        $nav['views'][$mode] = $nav['actions'][$mode];
        $nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
        $nav['views'][$mode]['primary'] = true;
        unset( $nav['actions'][$mode] );
      }
    }

    $xmlID = '';

    foreach ( $nav as $section => $links ) {
      foreach ( $links as $key => $link ) {
        if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
          $link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
        }

        $xmlID = isset($link['id']) ? $link['id'] : 'ca-' . $xmlID;
        $nav[$section][$key]['attributes'] = ' id="' . Sanitizer::escapeId( $xmlID ) . '"';

        if ($link['class']) {
          $nav[$section][$key]['attributes'] = $nav[$section][$key]['attributes'] .
              ' class="' . htmlspecialchars( $link['class'] ) . '"';
          $nav[$section][$key]['class'] = '';
        }

        if (isset($link['tooltiponly']) && $link['tooltiponly']) {
          $nav[$section][$key]['key'] = Linker::tooltip( $xmlID );
        } else {
          $nav[$section][$key]['key'] = Xml::expandAttributes(Linker::tooltipAndAccesskeyAttribs($xmlID));
        }
      }
    }

    $this->data['namespace_urls'] = $nav['namespaces'];
    $this->data['view_urls'] = $nav['views'];
    $this->data['action_urls'] = $nav['actions'];
    $this->data['variant_urls'] = $nav['variants'];


    // Reverse horizontally rendered navigation elements
    if ( $this->data['rtl'] ) {
        $this->data['view_urls'] = array_reverse( $this->data['view_urls'] );
        $this->data['namespace_urls'] = array_reverse( $this->data['namespace_urls'] );
        $this->data['personal_urls'] = array_reverse( $this->data['personal_urls'] );
    }

    // Output HTML Page
    $this->html( 'headelement' );
?>

  <main class="body">
    <header class="header clearfix d-print-none">
      <nav class="navbar">
        <div class="breadcrumb navbar-nav">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <li class="breadcrumb-link">
            <?php $this->html( 'title' ) ?>
          </li>
          <?php $this->renderNavigation( 'SEARCH' ); ?>
        <div>
      </nav>
    </header>
    <aside class="sidebar noprint" valign="top">
      <div id="sidebar-header" class="menu-box">
        <div class="menu-title">
          <h2>
            <a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>">
              <?php echo wfMessage( 'sitetitle' )->escaped(); ?>
            </a>
          </h2>
        </div>
      </div>
      <div class="menu-box">
        <div class="menu-title">
          <h2>Actions</h2>
        </div>
        <div class="menu-content">
          <ul>
            <?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS' ) ); ?>
          </ul>
          <ul class="mt-2">
            <?php $this->renderNavigation( 'NAMESPACES' ); ?>
          </ul>
        </div>
      </div>
      <?php
        $this->renderNavigation('VARIANTS');
        $this->renderPortals($this->data['sidebar']);
        $this->renderNavigation('PERSONAL');
      ?>
    </aside>
    <article class="content">
      <div id="mw-js-message" class="alert alert-info" style="display:none;"
        <?php $this->html( 'userlangattributes' ) ?>>
      </div>

      <?php if ( $this->data['sitenotice'] ): ?>
        <div id="siteNotice">
          <?php $this->html( 'sitenotice' ) ?>
        </div>
      <?php endif; ?>

      <div id="contentSub" class="mb-3"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>

      <?php if ( $this->data['undelete'] ): ?>
        <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
      <?php endif; ?>

      <?php if( $this->data['newtalk'] ): ?>
        <div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
      <?php endif; ?>

      <?php $this->html( 'bodycontent' ) ?>

      <?php if ( $this->data['printfooter'] ): ?>
        <div class="printfooter">
          <?php $this->html( 'printfooter' ); ?>
        </div>
      <?php endif; ?>

      <?php if ( $this->data['catlinks'] ): ?>
        <?php $this->html( 'catlinks' ); ?>
      <?php endif; ?>

      <?php if ( $this->data['dataAfterContent'] ): ?>
        <?php $this->html( 'dataAfterContent' ); ?>
      <?php endif; ?>

      <div class="float-clear"></div>

      <?php $this->html( 'debughtml' ); ?>

      <div class="pb-2">
      <?php
        foreach( $this->getFooterLinks() as $category => $links ) {
          if ( $category == 'info' ) {?>
             <br />
             <div class="page-info float-none">
               <?php foreach( $links as $link ): ?>
                 <?php $this->html( $link ) ?>
               <?php endforeach; ?>
             </div>
            <?php
          }
        }
      ?>
      </div>
    </article>
  </main>

  <?php $this->printTrail(); ?>
  <!-- <script type="text/javascript" src="https://cdn.kde.org/aether/js/jquery.min.js"></script>
  <script type="text/javascript" src="/skins/Aether/resources/jquery.ba-throttle-debounce.min.js"></script>
  <script type="text/javascript" src="https://cdn.kde.org/aether/js/popper.min.js"></script>
  <script type="text/javascript" src="/skins/Aether/resources/main.js"></script> -->
  <script>
    const toggler = document.querySelector('.navbar-toggler');
    const body = document.querySelector('.body');
    body.classList.remove('no-js');
    toggler.addEventListener('click', () => {
      body.classList.toggle('sidebar-active');
    });
  </script>
</body>
</html>

<?php
  }

  /**
   * Render a series of portals
   *
   * @param $portals array
   */
  private function renderPortals( $portals ) {
    // Force the rendering of the following portals
    if (!isset( $portals['SEARCH'])) {
      $portals['SEARCH'] = true;
    }
    if (!isset( $portals['TOOLBOX'])) {
      $portals['TOOLBOX'] = true;
    }
    if (!isset( $portals['LANGUAGES'])) {
      $portals['LANGUAGES'] = true;
    }
    // Render portals
    foreach ($portals as $name => $content) {
      if ($content === false) {
        continue;
      }

      switch($name) {
        case 'SEARCH':
          break;
        case 'TOOLBOX':
          $this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
          break;
        case 'LANGUAGES':
          if ($this->data['language_urls']) {
             $this->renderPortal('lang', $this->data['language_urls'], 'otherlanguages');
          }
          break;
        default:
          $this->renderPortal($name, $content);
        break;
      }
    }
  }

  protected function renderPortal($name, $content, $msg = null, $hook = null) {
    if ( $msg === null ) {
      $msg = $name;
    }
    ?>
    <div class="menu-box">
      <div class="menu-title" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>' <?php echo Linker::tooltip( 'p-' . $name ) ?>>
        <h2><?php $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></h2>
      </div>
      <div class="menu-content">
        <?php
          if (is_array( $content)) {
            echo "<ul>";
              foreach( $content as $key => $val ) {
                echo $this->makeListItem( $key, $val );
              }

              if ( $hook !== null ) {
                Hooks::run( $hook, array( &$this, true ) );
              }
            echo "</ul>";
          } else {
            echo $content; /* Allow raw HTML block to be defined by extensions */
          }
        ?>
      </div>
    </div>
    <?php 
  }

  /**
   * Render one or more navigations elements by name, automatically reveresed
   * when UI is in RTL mode
   *
   * @param $elements array
   */
  private function renderNavigation( $elements ) {
    global $wgNeverlandUseSimpleSearch;

    // If only one element was given, wrap it in an array, allowing more
    // flexible arguments
    if ( !is_array( $elements ) ) {
      $elements = array( $elements );
      // If there's a series of elements, reverse them when in RTL mode
    } elseif ( $this->data['rtl'] ) {
      $elements = array_reverse( $elements );
    }
    // Render elements
    foreach ( $elements as $name => $element ) {
      switch ( $element ) {
        case 'NAMESPACES':
          if (count( $this->data['namespace_urls'] ) > 0) {
            foreach ( $this->data['namespace_urls'] as $link ) {
              if ( stripos( $link['attributes'], 'selected' ) === false ) { ?>
                <li>
                  <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
                    <?php echo htmlspecialchars( $link['text'] ) ?>
                  </a>
                </li>
              <?php } else { ?>
                <li>
                  <?php echo htmlspecialchars( $link['text'] ) ?>
                </li>
              <?php
              }
            }
          }
          break;

        case 'VARIANTS':
        if ( count( $this->data['variant_urls'] ) > 0 ) { ?>
            <div class="menu-box">
              <div class="menu-title">
                <h2><?php $this->msg( 'variants' ) ?></h2>
              </div>
              <div class="menu-content">
                <ul>
                  <?php foreach ( $this->data['variant_urls'] as $link ) { ?>
                    <li class="list-group-item" <?php echo $link['attributes'] ?>>
                        <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
                            <?php echo htmlspecialchars( $link['text'] ) ?>
                        </a>
                    </li>
                  <?php } ?>
                </ul>
              </div>
            </div>
        <?php
        }
        break;

        case 'VIEWS':
          if (count( $this->data['view_urls'] ) > 0 ) {
            foreach ( $this->data['view_urls'] as $link ) { ?>
              <li>
                <?php if (stripos($link['attributes'], 'selected') && strlen($link['text']) > 1) {
                   echo htmlspecialchars( $link['text'] );
                } else if (strlen($link['text']) > 1) { ?>
                  <a href="<?php echo htmlspecialchars($link['href']) ?>">
                    <?php echo htmlspecialchars( $link['text'] ); ?>
                  </a>
                <?php } ?>
              </li>
            <?php }
          }
          break;

        case 'ACTIONS':
          foreach ($this->data['action_urls'] as $link) { ?>
            <a <?= $link['attributes'] ?> href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
              <?php echo htmlspecialchars($link['text']) ?>
            </a>
          <?php
          }
          break;

        case 'PERSONAL':
          if ( count( $this->data['personal_urls'] ) > 0 ) {
            ?>
            <div class="menu-box">
              <div class="menu-title">
                <h2><?php $this->msg( 'personaltools' ) ?></h2>
              </div>
              <div class="menu-content">
                <ul>
                  <?php foreach( $this->getPersonalTools() as $key => $item ) { ?>
                    <?php echo $this->makeListItem( $key, $item ); ?>
                  <?php } ?>
                </ul>
              </div>
            </div>
            <?php
          }
          break;

        case 'SEARCH': ?>
          <form action="<?php $this->text( 'wgScript' ) ?>" id="searchform" class="form-inline ml-auto">
            <input id="searchInput" name="search" type="search" placeholder="<?php $this->msg( 'search' ) ?>..."
                   class="form-control" autocomplete="off"
            <?php if( isset( $this->data['search'] ) ): ?>
              value="<?php $this->text( 'search' ) ?>"
            <?php endif; ?> />

            <input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>" />
          </form>
        <?php
        break;
      }
    }
  }
}
