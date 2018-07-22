<?php
/**
 * Header_Menu_Walker类
 * 这类名当然你随意了
 */
class Header_Menu_Walker extends Walker_Nav_Menu {

    /**
     * start_lvl函数
     * 这函数主要处理ul，如果ul有一些特殊的样式，修改这里
     * 他这里面的$depth就是层级，一级二级三级
     * $args是上面wp_nav_menu()函数定义的那个数组
     *
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // 缩进
        $display_depth = ( $depth + 1); // 层级默认是0级，这里+1为了从1开始算
        $classes = array(
            'dropdown-menu hidden-xs', //ul是个子菜单的时候，添加这个样式
            ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ), //子菜单奇数加样式menu-odd,偶数加样式menu-even
            ( $display_depth >=2 ? 'sub-sub-menu' : '' ),   //三级菜单的时候，添加这个样式
            'menu-depth-' . $display_depth, //这样式主要能看出当前菜单的层级，menu-depth-2是二级呗
        );
        $class_names = implode( ' ', $classes ); //用空格分割多个样式名

        $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n"; //把刚才定义的，那么多的样式，写到ul里面
    }

    /**
     * start_el函数
     * 主要处理li和里面的a
     * $depth和$args同上
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     * @param int    $id     Current item ID.
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $wp_query;
        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // 缩进

        // 定义li的样式
        $depth_classes = array(
            ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),   //一级的li，就main-menu-item，其余全部sub-menu-item
            ( $depth >=2 ? 'sub-sub-menu-item' : '' ),  //三级的li，添加这个样式
            ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),    //奇数加样式menu-item-odd,偶数加样式menu-item-even
            'menu-item-depth-' . $depth,    //层级同上
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) ); //这句我没看懂，不知道是在干啥

        // 把样式合成到li里面
        $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

        // 处理a的属性
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link"';

        //添加a的样式
        $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
            $args->before,
            $attributes,
            $args->link_before,
            apply_filters( 'the_title', $item->title, $item->ID ),
            $args->link_after,
            $args->after
        );
        //上面这个item_output我要说一下。这里写的有点死。
        //如果一级菜单是<a><span>我是菜单</span></a>
        //然而其他级菜单是<a><strong>我是菜单</strong></a>
        //这样的情况，$args->link_before是固定值就不行了，要自行判断
        //$link_before = $depth == 0 ? '<span>' : '<strong>';
        //$link_after = $depth == 0 ? '</span>' : '</strong>';
        //类似这个意思。
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}