<?php
/**
 * Template: contact-card.php
 * Variables disponibles:
 *   $area_name (string)    — Nombre del área
 *   $contacts  (array[])  — Lista de contactos del área
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="directorio-area">
    <div class="directorio-area__header">
        <span class="directorio-area__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </span>
        <h3 class="directorio-area__title"><?php echo esc_html( $area_name ); ?></h3>
        <span class="directorio-area__count"><?php echo esc_html( count( $contacts ) ); ?></span>
    </div>

    <div class="directorio-area__table-wrap">
        <table class="directorio-table" role="table" aria-label="<?php echo esc_attr( sprintf( __( 'Contactos del área %s', 'mc-intranet-core' ), $area_name ) ); ?>">
            <thead>
                <tr>
                    <th scope="col" class="directorio-table__th directorio-table__th--nombre"><?php esc_html_e( 'Nombre', 'mc-intranet-core' ); ?></th>
                    <th scope="col" class="directorio-table__th directorio-table__th--cargo"><?php esc_html_e( 'Cargo', 'mc-intranet-core' ); ?></th>
                    <th scope="col" class="directorio-table__th directorio-table__th--celular"><?php esc_html_e( 'Celular', 'mc-intranet-core' ); ?></th>
                    <th scope="col" class="directorio-table__th directorio-table__th--email"><?php esc_html_e( 'Email', 'mc-intranet-core' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $contacts as $contact ) : ?>
                    <?php
                    $initials = '';
                    $parts    = explode( ' ', trim( $contact['nombre'] ), 3 );
                    foreach ( $parts as $part ) {
                        $initials .= strtoupper( mb_substr( $part, 0, 1 ) );
                        if ( strlen( $initials ) >= 2 ) {
                            break;
                        }
                    }
                    $celular_digits = preg_replace( '/[^0-9+]/', '', $contact['celular'] );
                    ?>
                    <tr class="directorio-table__row">
                        <td class="directorio-table__td directorio-table__td--nombre">
                            <div class="directorio-contact">
                                <div class="directorio-contact__avatar" aria-hidden="true"><?php echo esc_html( $initials ); ?></div>
                                <span class="directorio-contact__name"><?php echo esc_html( $contact['nombre'] ); ?></span>
                            </div>
                        </td>
                        <td class="directorio-table__td directorio-table__td--cargo">
                            <span class="directorio-contact__cargo"><?php echo esc_html( $contact['cargo'] ); ?></span>
                        </td>
                        <td class="directorio-table__td directorio-table__td--celular">
                            <?php if ( $celular_digits ) : ?>
                                <a class="directorio-contact__link directorio-contact__link--phone"
                                   href="tel:<?php echo esc_attr( $celular_digits ); ?>"
                                   aria-label="<?php echo esc_attr( sprintf( __( 'Llamar a %s', 'mc-intranet-core' ), $contact['nombre'] ) ); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    <?php echo esc_html( $contact['celular'] ); ?>
                                </a>
                            <?php else : ?>
                                <span class="directorio-contact__empty">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="directorio-table__td directorio-table__td--email">
                            <?php if ( $contact['email'] ) : ?>
                                <a class="directorio-contact__link directorio-contact__link--email"
                                   href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"
                                   aria-label="<?php echo esc_attr( sprintf( __( 'Enviar email a %s', 'mc-intranet-core' ), $contact['nombre'] ) ); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                    <?php echo esc_html( $contact['email'] ); ?>
                                </a>
                            <?php else : ?>
                                <span class="directorio-contact__empty">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* ─── Directorio de Contactos ───────────────────────────────────────────── */
.directorio-contactos {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    font-family: var(--font-body, 'Inter', system-ui, sans-serif);
}

.directorio-area {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
}

.directorio-area__header {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.25rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
}

.directorio-area__icon {
    color: var(--color-primary, #1A2E52);
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

.directorio-area__title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
    flex: 1;
}

.directorio-area__count {
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b7280;
    background: #e5e7eb;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
}

.directorio-area__table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.directorio-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.directorio-table__th {
    text-align: left;
    padding: 0.625rem 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}

.directorio-table__row {
    transition: background 0.15s ease;
}

.directorio-table__row:not(:last-child) {
    border-bottom: 1px solid #f3f4f6;
}

.directorio-table__row:hover {
    background: #f9fafb;
}

.directorio-table__td {
    padding: 0.75rem 1rem;
    color: #374151;
    vertical-align: middle;
}

.directorio-contact {
    display: flex;
    align-items: center;
    gap: 0.625rem;
}

.directorio-contact__avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: var(--color-primary, #1A2E52);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    letter-spacing: 0.025em;
}

.company--anstra .directorio-contact__avatar    { background: #1A2E52; }
.company--essenza .directorio-contact__avatar   { background: #1B6B45; }
.company--budefry .directorio-contact__avatar   { background: #7C3AED; }
.company--interactua .directorio-contact__avatar { background: #D97706; }

.directorio-contact__name {
    font-weight: 500;
    color: #111827;
}

.directorio-contact__cargo {
    color: #6b7280;
}

.directorio-contact__link {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    color: var(--color-primary, #1A2E52);
    text-decoration: none;
    font-size: 0.8125rem;
    transition: color 0.15s ease, opacity 0.15s ease;
}

.directorio-contact__link:hover {
    opacity: 0.75;
    text-decoration: underline;
}

.directorio-contact__empty {
    color: #d1d5db;
}

@media (max-width: 640px) {
    .directorio-table__th--cargo,
    .directorio-table__td--cargo {
        display: none;
    }

    .directorio-area__header {
        padding: 0.75rem 1rem;
    }

    .directorio-table__th,
    .directorio-table__td {
        padding: 0.625rem 0.75rem;
    }
}
</style>
