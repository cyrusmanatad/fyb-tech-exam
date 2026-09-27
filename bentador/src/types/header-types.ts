// ─────────────────────────────────────────────────────────────
// Breadcrumb
// ─────────────────────────────────────────────────────────────

import type { PlusIcon } from '@heroicons/vue/24/outline'

export interface BreadcrumbItem {
  /** Visible text */
  label: string
  /** If provided, renders as a <RouterLink> */
  to?: string
}

// ─────────────────────────────────────────────────────────────
// DropdownItem — a single row inside a dropdown menu
// ─────────────────────────────────────────────────────────────

export interface DropdownItem {
  label: string
  icon?: object
  handler?: () => void
  danger?: boolean
  disabled?: boolean
  /** Renders a visual divider row instead of a button */
  divider?: boolean
}

// ─────────────────────────────────────────────────────────────
// ActionItem — discriminated union
//
// Pass an array of these to :action-items on <AppHeader> or
// <HeaderActions>. Each entry declares its own `type` so the
// renderer knows exactly which element to produce.
//
//   type: 'button'    →  HeaderButton   (primary / secondary CTA)
//   type: 'dropdown'  →  AppDropdown    (trigger + menu of rows)
// ─────────────────────────────────────────────────────────────

interface ActionBase {
  /** Used as :key in v-for — must be unique within the array */
  key: string
}

export interface ButtonAction extends ActionBase {
  type: 'button'
  label: string
  /** Any Heroicon / Lucide component. Omit for no icon. */
  icon?: typeof PlusIcon | null
  variant?: 'primary' | 'secondary'
  disabled?: boolean
  handler: () => void
}

export interface DropdownAction extends ActionBase {
  type: 'dropdown'
  /** Trigger button label */
  label: string
  /** Icon on the trigger button itself (optional) */
  icon?: typeof PlusIcon | null
  align?: 'left' | 'right'
  items: DropdownItem[]
  /** Fired when a child row is selected */
  onSelect?: (item: DropdownItem) => void
}

/** The only type you need to import at the page level */
export type ActionItem = ButtonAction | DropdownAction
