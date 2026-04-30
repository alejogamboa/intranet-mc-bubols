<?php
/**
 * Template: contact-directory-tabs.php
 * Variables disponibles:
 *   $groups       (array<string, array>) Lista de contactos agrupados por area
 *   $all_contacts (array<int, array>)    Lista plana de contactos
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$instance_id = 'directorio-' . wp_rand( 1000, 999999 );
?>
<div id="<?php echo esc_attr( $instance_id ); ?>" class="directorio-contactos-tabs company--<?php echo esc_attr( $empresa ?: 'mc' ); ?>">
    <div class="directorio-toolbar">
        <div class="directorio-search-wrap">
            <label class="screen-reader-text" for="<?php echo esc_attr( $instance_id ); ?>-search"><?php esc_html_e( 'Buscar contacto', 'mc-intranet-core' ); ?></label>
            <input
                id="<?php echo esc_attr( $instance_id ); ?>-search"
                class="directorio-search"
                type="search"
                placeholder="<?php esc_attr_e( 'Buscar por nombre, cargo, celular o email', 'mc-intranet-core' ); ?>"
                autocomplete="off"
            />
        </div>
    </div>

    <div class="directorio-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Areas del directorio', 'mc-intranet-core' ); ?>">
        <?php
        $tab_index = 0;
        foreach ( $groups as $area_name => $contacts ) :
            $is_active = 0 === $tab_index;
            ?>
            <button
                type="button"
                class="directorio-tab<?php echo $is_active ? ' is-active' : ''; ?>"
                role="tab"
                aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                data-area="<?php echo esc_attr( $area_name ); ?>"
            >
                <span><?php echo esc_html( $area_name ); ?></span>
                <span class="directorio-tab__count"><?php echo esc_html( (string) count( $contacts ) ); ?></span>
            </button>
            <?php
            $tab_index++;
        endforeach;
        ?>
    </div>

    <div class="directorio-table-wrap">
        <table class="directorio-table" role="table">
            <thead>
                <tr>
                    <th scope="col"><?php esc_html_e( 'Nombre', 'mc-intranet-core' ); ?></th>
                    <th scope="col" class="directorio-col-area"><?php esc_html_e( 'Area', 'mc-intranet-core' ); ?></th>
                    <th scope="col" class="directorio-col-cargo"><?php esc_html_e( 'Cargo', 'mc-intranet-core' ); ?></th>
                    <th scope="col"><?php esc_html_e( 'Celular', 'mc-intranet-core' ); ?></th>
                    <th scope="col"><?php esc_html_e( 'Email', 'mc-intranet-core' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $all_contacts as $contact ) : ?>
                    <?php
                    $area_name = $contact['area'] ?: __( 'Sin area', 'mc-intranet-core' );
                    $initials  = '';
                    $parts     = preg_split( '/\s+/', trim( (string) $contact['nombre'] ) );
                    if ( is_array( $parts ) ) {
                        foreach ( $parts as $part ) {
                            if ( '' === $part ) {
                                continue;
                            }
                            $initials .= strtoupper( mb_substr( $part, 0, 1 ) );
                            if ( strlen( $initials ) >= 2 ) {
                                break;
                            }
                        }
                    }

                    $search_text = implode(
                        ' ',
                        [
                            (string) $contact['nombre'],
                            (string) $contact['cargo'],
                            (string) $contact['celular'],
                            (string) $contact['email'],
                            (string) $area_name,
                        ]
                    );

                    $celular_digits = preg_replace( '/[^0-9+]/', '', (string) $contact['celular'] );
                    ?>
                    <tr class="directorio-row" data-area="<?php echo esc_attr( $area_name ); ?>" data-search="<?php echo esc_attr( $search_text ); ?>">
                        <td>
                            <div class="directorio-contact">
                                <div class="directorio-contact__avatar" aria-hidden="true"><?php echo esc_html( $initials ?: '--' ); ?></div>
                                <span class="directorio-contact__name"><?php echo esc_html( (string) $contact['nombre'] ); ?></span>
                            </div>
                        </td>
                        <td class="directorio-col-area"><span class="directorio-pill"><?php echo esc_html( $area_name ); ?></span></td>
                        <td class="directorio-col-cargo"><?php echo esc_html( (string) $contact['cargo'] ); ?></td>
                        <td>
                            <?php if ( $celular_digits ) : ?>
                                <a class="directorio-link" href="tel:<?php echo esc_attr( $celular_digits ); ?>"><?php echo esc_html( (string) $contact['celular'] ); ?></a>
                            <?php else : ?>
                                <span class="directorio-empty">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( ! empty( $contact['email'] ) ) : ?>
                                <a class="directorio-link" href="mailto:<?php echo esc_attr( (string) $contact['email'] ); ?>"><?php echo esc_html( (string) $contact['email'] ); ?></a>
                            <?php else : ?>
                                <span class="directorio-empty">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="directorio-empty-state" hidden><?php esc_html_e( 'No hay resultados para esta busqueda.', 'mc-intranet-core' ); ?></p>
    </div>
</div>

<style>
.directorio-contactos-tabs {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    font-family: var(--font-body, "Inter", system-ui, sans-serif);
}

.directorio-toolbar {
    display: flex;
    justify-content: flex-end;
}

.directorio-search-wrap {
    width: min(420px, 100%);
}

.directorio-search {
    width: 100%;
    height: 42px;
    padding: 0 0.875rem;
    border: 1px solid #d1d5db;
    border-radius: 0.625rem;
    font-size: 0.9rem;
    background: #fff;
}

.directorio-search:focus {
    outline: 2px solid transparent;
    border-color: #1a2e52;
    box-shadow: 0 0 0 3px rgba(26, 46, 82, 0.18);
}

.directorio-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.directorio-tab {
    border: 1px solid #d1d5db;
    background: #fff;
    color: #374151;
    border-radius: 9999px;
    padding: 0.45rem 0.75rem;
    font-size: 0.8125rem;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    cursor: pointer;
    transition: all 0.15s ease;
}

.directorio-tab:hover {
    border-color: #9ca3af;
}

.directorio-tab.is-active {
    color: #fff;
    border-color: var(--color-primary, #1a2e52);
    background: var(--color-primary, #1a2e52);
}

.directorio-tab.has-match-hint {
    border-color: #0ea5e9;
    background: #f0f9ff;
    color: #0c4a6e;
    box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.12);
}

.directorio-tab.has-match-hint .directorio-tab__count {
    background: #0ea5e9;
    color: #fff;
    animation: directorioPulse 1.4s ease-in-out infinite;
}

.directorio-tab.no-match:not(.is-active) {
    opacity: 0.58;
}

.directorio-tab__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.35rem;
    height: 1.35rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.24);
    font-size: 0.6875rem;
    padding: 0 0.3rem;
}

@keyframes directorioPulse {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.08);
    }
}

.directorio-table-wrap {
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    overflow: hidden;
    background: #fff;
}

.directorio-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.directorio-table th {
    text-align: left;
    padding: 0.625rem 0.875rem;
    background: #f9fafb;
    color: #6b7280;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 1px solid #e5e7eb;
}

.directorio-table td {
    padding: 0.7rem 0.875rem;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
}

.directorio-row:hover {
    background: #fbfdff;
}

.directorio-contact {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.directorio-contact__avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    background: var(--color-primary, #1a2e52);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.company--anstra .directorio-contact__avatar { background: #1a2e52; }
.company--essenza .directorio-contact__avatar { background: #1b6b45; }
.company--budefry .directorio-contact__avatar { background: #7c3aed; }
.company--interactua .directorio-contact__avatar { background: #d97706; }

.directorio-contact__name {
    font-weight: 500;
    color: #111827;
}

.directorio-pill {
    display: inline-flex;
    padding: 0.2rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.72rem;
    color: #374151;
    background: #f3f4f6;
}

.directorio-link {
    color: var(--color-primary, #1a2e52);
    text-decoration: none;
}

.directorio-link:hover {
    text-decoration: underline;
}

.directorio-empty {
    color: #9ca3af;
}

.directorio-empty-state {
    margin: 0;
    padding: 1rem;
    font-size: 0.9rem;
    color: #6b7280;
}

@media (max-width: 860px) {
    .directorio-col-cargo {
        display: none;
    }
}

@media (max-width: 640px) {
    .directorio-col-area {
        display: none;
    }

    .directorio-table th,
    .directorio-table td {
        padding: 0.6rem 0.6rem;
    }
}
</style>

<script>
(function () {
    var root = document.getElementById(<?php echo wp_json_encode( $instance_id ); ?>);
    if (!root) {
        return;
    }

    var tabs = root.querySelectorAll('.directorio-tab');
    var rows = root.querySelectorAll('.directorio-row');
    var searchInput = root.querySelector('.directorio-search');
    var emptyState = root.querySelector('.directorio-empty-state');
    var activeArea = tabs.length ? tabs[0].getAttribute('data-area') : '';

    tabs.forEach(function (tab) {
        var countEl = tab.querySelector('.directorio-tab__count');
        var total = countEl ? parseInt(countEl.textContent || '0', 10) : 0;
        tab.setAttribute('data-total', String(isNaN(total) ? 0 : total));
    });

    function normalize(text) {
        return (text || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function applyFilters() {
        var term = normalize(searchInput ? searchInput.value : '');
        var visible = 0;
        var matchesByArea = Object.create(null);

        rows.forEach(function (row) {
            var area = row.getAttribute('data-area') || '';
            var searchable = normalize(row.getAttribute('data-search') || '');
            var matchesArea = !activeArea || area === activeArea;
            var matchesTerm = !term || searchable.indexOf(term) !== -1;
            var show = matchesArea && matchesTerm;

            if (matchesTerm) {
                matchesByArea[area] = (matchesByArea[area] || 0) + 1;
            }

            row.hidden = !show;
            if (show) {
                visible++;
            }
        });

        tabs.forEach(function (tab) {
            var area = tab.getAttribute('data-area') || '';
            var areaMatches = matchesByArea[area] || 0;
            var isActive = area === activeArea;
            var countEl = tab.querySelector('.directorio-tab__count');
            var total = parseInt(tab.getAttribute('data-total') || '0', 10);

            tab.classList.toggle('has-match-hint', !!term && !isActive && areaMatches > 0);
            tab.classList.toggle('no-match', !!term && areaMatches === 0);

            if (countEl) {
                countEl.textContent = term ? String(areaMatches) : String(isNaN(total) ? 0 : total);
            }
        });

        if (emptyState) {
            emptyState.hidden = visible > 0;
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activeArea = tab.getAttribute('data-area') || '';
            tabs.forEach(function (btn) {
                var selected = btn === tab;
                btn.classList.toggle('is-active', selected);
                btn.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    applyFilters();
})();
</script>
