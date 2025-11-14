<?php
session_start();

require_once __DIR__ . '/../util/conexion.php';

class Control_Menu {
    private $db;

    public function __construct() {
        $this->db = Conexion::obtenerConexion();
    }
    public function generarMenuHtml() {
        $roles = [];
        if (isset($_SESSION['usuario']['roles']) && is_array($_SESSION['usuario']['roles'])) {
            $roles = $_SESSION['usuario']['roles'];
        } else {
            $roles = [];
        }

        if (empty($roles)) {
            $sql = "SELECT m.* FROM menu m
                    LEFT JOIN menurol mr ON mr.idmenu = m.idmenu
                    WHERE mr.idmenu IS NULL
                    AND (m.medeshabilitado IS NULL OR m.medeshabilitado = '0000-00-00 00:00:00')
                    ORDER BY m.idpadre, m.idmenu";
            $menus = $this->db->query($sql)->fetchAll();
        } else {
            $in = implode(',', array_map('intval', $roles));
            $sql = "SELECT DISTINCT m.* FROM menu m
                    JOIN menurol mr ON mr.idmenu = m.idmenu
                    WHERE mr.idrol IN ($in)
                    AND (m.medeshabilitado IS NULL OR m.medeshabilitado = '0000-00-00 00:00:00')
                    ORDER BY m.idpadre, m.idmenu";
            $stmt = $this->db->query($sql);
            $menus = $stmt->fetchAll();
        }

        $tree = [];
        $byId = [];
        foreach ($menus as $m) {
            $m['children'] = [];
            $byId[$m['idmenu']] = $m;
        }
        foreach ($byId as $id => $m) {
            $parent = $m['idpadre'];
            if ($parent && isset($byId[$parent])) {
                $byId[$parent]['children'][] = &$byId[$id];
            } else {
                $tree[] = &$byId[$id];
            }
        }

        $html = '<ul class="menu">';
        foreach ($tree as $node) {
            $html .= $this->renderNode($node);
        }
        $html .= '</ul>';
        return $html;
    }

    private function renderNode($node) {
        $label = htmlspecialchars($node['menombre']);
        $href = '#';
        if (!empty($node['medescripcion'])) {
            $href = '#';
        }

        $html = '<li class="menu-item">';
        $html .= "<a href=\"{$href}\">{$label}</a>";
        if (!empty($node['children'])) {
            $html .= '<ul class="submenu">';
            foreach ($node['children'] as $child) {
                $html .= $this->renderNode($child);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    public function obtenerMenuAjax() {
        header('Content-Type: application/json; charset=utf-8');
        $html = $this->generarMenuHtml();
        echo json_encode(['success' => true, 'html' => $html]);
    }
}