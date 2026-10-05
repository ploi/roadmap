<?php

return [
    'title' => 'MCP server',
    'description' => 'Connect AI assistants such as Claude, ChatGPT or Cursor to :app through the Model Context Protocol (MCP). Your assistant can then look up projects and items, read the discussion and comment for you.',
    'disabled' => 'The MCP server is disabled, users can\'t see this page or connect. Enable it in the settings under "MCP".',

    'permissions-title' => 'What your assistant can do',
    'permissions' => 'The assistant acts as you. It sees exactly the projects, items and comments you can see on the roadmap, and comments and items are posted under your name. Only admins and employees can move items between boards.',

    'step-token-title' => '1. Create a token',
    'step-token' => 'Create a personal token in your profile. You can revoke it at any time, which disconnects every assistant that uses it.',
    'step-token-button' => 'Create a token',
    'step-token-login' => 'Log in to create a token',

    'step-connect-title' => '2. Connect your assistant',
    'step-connect' => 'Pick your AI client and follow the steps. The token is sent as a bearer token with every request.',
    'endpoint' => 'Endpoint',
    'copy' => 'Copy',
    'copied' => 'Copied!',
    'run-in-terminal' => 'Run in your terminal',

    'clients' => [
        'claude-code' => [
            'Run the command below in your terminal, with <code>YOUR_TOKEN</code> replaced by your token.',
            'Start Claude Code and run <code>/mcp</code> to check that the server is connected.',
        ],
        'claude-desktop' => [
            'Make sure <a href="https://nodejs.org" target="_blank" class="underline">Node.js</a> is installed, Claude Desktop uses it to connect to the server.',
            'Open Claude Desktop and go to <strong>Settings → Developer → Edit config</strong>.',
            'Add the server below to <code>claude_desktop_config.json</code>, with <code>YOUR_TOKEN</code> replaced by your token.',
            'Restart Claude Desktop, the roadmap tools show up in the tools menu of the chat.',
        ],
        'claude-web' => 'The web version of Claude (claude.ai) only supports connectors that sign in with OAuth, which the roadmap does not offer yet. Use Claude Desktop or Claude Code instead.',
        'chatgpt' => [
            'Open ChatGPT and go to <strong>Settings → Apps & Connectors → Advanced settings</strong>, and turn on <strong>Developer mode</strong>.',
            'Go back to <strong>Apps & Connectors</strong> and click <strong>Create</strong>.',
            'Give the connector a name, and use the URL below as the MCP server URL.',
            'Choose <strong>Access token / API key</strong> as authentication and paste your token.',
            'Enable the connector in a new chat through the <strong>+</strong> menu.',
        ],
        'chatgpt-url' => 'MCP server URL',
        'chatgpt-note' => 'Developer mode is only available on some ChatGPT plans, and ChatGPT must be able to reach the roadmap over the internet.',
        'cursor' => [
            'Open <code>~/.cursor/mcp.json</code>, or <strong>Cursor Settings → MCP → Add new MCP server</strong>.',
            'Add the server below, with <code>YOUR_TOKEN</code> replaced by your token.',
            'The roadmap tools become available in the Cursor agent.',
        ],
        'vscode' => [
            'Create <code>.vscode/mcp.json</code> in your project, or run <strong>MCP: Open User Configuration</strong> from the command palette to add it for all projects.',
            'Add the server below, with <code>YOUR_TOKEN</code> replaced by your token.',
            'Open Copilot Chat in agent mode, the roadmap tools show up in the tools picker.',
        ],
        'codex' => [
            'Add the server below to <code>~/.codex/config.toml</code>.',
            'Store your token in the <code>ROADMAP_TOKEN</code> environment variable, for example in your shell profile.',
            'Start Codex and run <code>/mcp</code> to check that the server is connected.',
        ],
    ],

    'step-use-title' => '3. Ask away',
    'step-use' => 'Ask your assistant things like "What is planned for the next release?", "Summarise the discussion on the dark mode request" or "Move the CSV export item to In progress".',

    'tools-title' => 'Available tools',
];
