// Adds `@` autocomplete to a Filament markdown editor (EasyMDE / CodeMirror 5).
// Usage: x-mentions="{ url: '/mention-search', item: 18 }" on the editor wrapper.
// The inserted `@Full Name` is turned into a profile link by the MentionParser when the comment is saved.

const MAX_WORDS = 4
const TRIGGER = /(?:^|[\s(\[])@([^@\n]{0,40})$/
const TRAILING_PUNCTUATION = /[.,!?;:)'"]$/

const mentions = (el, { expression }, { evaluate, cleanup }) => {
  const { url, item } = evaluate(expression)

  let codemirror = null
  let dropdown = null
  let results = []
  let activeIndex = 0
  let triggerPosition = null
  let lastQuery = null
  let requestId = 0
  let debounceTimer = null

  const attach = () => {
    if (codemirror || !el._editor?.codemirror) {
      return
    }

    codemirror = el._editor.codemirror
    codemirror.on('cursorActivity', onCursorActivity)
    codemirror.on('blur', close)
  }

  const onCursorActivity = () => {
    if (!codemirror.hasFocus()) {
      return
    }

    const cursor = codemirror.getCursor()
    const match = codemirror.getLine(cursor.line).slice(0, cursor.ch).match(TRIGGER)

    if (!match || match[1].trim().split(/\s+/).length > MAX_WORDS) {
      close()
      return
    }

    triggerPosition = { line: cursor.line, ch: cursor.ch - match[1].length - 1 }
    search(match[1].trim())
  }

  const search = (query) => {
    if (query === lastQuery && dropdown) {
      position()
      return
    }

    lastQuery = query
    clearTimeout(debounceTimer)

    debounceTimer = setTimeout(async () => {
      const currentRequest = ++requestId
      const params = new URLSearchParams({ query })

      if (item) {
        params.set('item', item)
      }

      try {
        const response = await fetch(`${url}?${params}`, {
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
        const users = response.ok ? await response.json() : []

        if (currentRequest === requestId && triggerPosition) {
          show(users)
        }
      } catch {
        close()
      }
    }, 150)
  }

  const show = (users) => {
    results = users
    activeIndex = 0

    if (!results.length) {
      removeDropdown()
      return
    }

    if (!dropdown) {
      dropdown = document.createElement('div')
      dropdown.setAttribute('role', 'listbox')
      dropdown.className = 'fixed w-64 max-h-64 overflow-y-auto p-1 rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 text-sm dark:bg-gray-900 dark:ring-white/10'
      dropdown.style.zIndex = 1000
      document.body.appendChild(dropdown)
    }

    render()
    position()
  }

  const render = () => {
    dropdown.replaceChildren(...results.map((user, index) => {
      const option = document.createElement('button')
      option.type = 'button'
      option.setAttribute('role', 'option')
      option.setAttribute('aria-selected', index === activeIndex)
      option.className = 'flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-left ' + (
        index === activeIndex ? 'bg-gray-100 dark:bg-white/5' : 'hover:bg-gray-50 dark:hover:bg-white/5'
      )

      const avatar = document.createElement('img')
      avatar.src = user.avatar
      avatar.alt = ''
      avatar.className = 'w-6 h-6 shrink-0 rounded-full object-cover'

      const name = document.createElement('span')
      name.textContent = user.key
      name.className = 'truncate font-medium text-gray-900 dark:text-white'

      const username = document.createElement('span')
      username.textContent = `@${user.value}`
      username.className = 'ml-auto shrink-0 truncate text-xs text-gray-500 dark:text-gray-400'

      option.append(avatar, name, username)
      option.addEventListener('mousedown', (event) => {
        event.preventDefault()
        select(index)
      })

      return option
    }))

    dropdown.children[activeIndex]?.scrollIntoView({ block: 'nearest' })
  }

  const position = () => {
    if (!dropdown || !triggerPosition) {
      return
    }

    const coords = codemirror.cursorCoords(triggerPosition, 'window')
    const left = Math.min(coords.left, window.innerWidth - dropdown.offsetWidth - 8)
    const fitsBelow = coords.bottom + dropdown.offsetHeight + 4 < window.innerHeight

    dropdown.style.left = `${Math.max(8, left)}px`
    dropdown.style.top = `${fitsBelow ? coords.bottom + 4 : coords.top - dropdown.offsetHeight - 4}px`
  }

  // Prefer the full name, but fall back to the username when the name is ambiguous or won't be recognized on save.
  const mentionText = (user) => {
    const isAmbiguous = results.filter((result) => result.key.toLowerCase() === user.key.toLowerCase()).length > 1
    const isRecognizable = /^[\p{L}\p{N}_]/u.test(user.key)
      && !TRAILING_PUNCTUATION.test(user.key)
      && !user.key.includes('@')
      && user.key.trim().split(/\s+/).length <= MAX_WORDS

    return !isAmbiguous && isRecognizable ? user.key.trim() : user.value
  }

  const select = (index) => {
    const user = results[index]

    if (!user || !triggerPosition) {
      return
    }

    codemirror.replaceRange(`@${mentionText(user)} `, triggerPosition, codemirror.getCursor())
    close()
    codemirror.focus()
  }

  const removeDropdown = () => {
    dropdown?.remove()
    dropdown = null
    results = []
  }

  const close = () => {
    clearTimeout(debounceTimer)
    requestId++
    triggerPosition = null
    lastQuery = null
    removeDropdown()
  }

  // Handle navigation keys before CodeMirror (and Filament modals) see them, but only while the dropdown is open.
  const onKeydown = (event) => {
    if (!dropdown) {
      return
    }

    const actions = {
      ArrowDown: () => { activeIndex = (activeIndex + 1) % results.length; render() },
      ArrowUp: () => { activeIndex = (activeIndex - 1 + results.length) % results.length; render() },
      Enter: () => select(activeIndex),
      Tab: () => select(activeIndex),
      Escape: close,
    }

    if (!actions[event.key]) {
      return
    }

    event.preventDefault()
    event.stopPropagation()
    actions[event.key]()
  }

  el.addEventListener('focusin', attach)
  el.addEventListener('keydown', onKeydown, true)
  window.addEventListener('scroll', position, true)
  window.addEventListener('resize', position)

  cleanup(() => {
    close()
    el.removeEventListener('focusin', attach)
    el.removeEventListener('keydown', onKeydown, true)
    window.removeEventListener('scroll', position, true)
    window.removeEventListener('resize', position)
  })
}

export { mentions }
