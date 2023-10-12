module.exports = {
  env: {
    browser: true,
    es2020: true,
  },
  extends: [
    'eslint:recommended',
    'plugin:@typescript-eslint/recommended',
    'plugin:react/recommended',
    'plugin:react-hooks/recommended',
    '@unocss',
    'prettier',
  ],
  parser: '@typescript-eslint/parser',
  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module',
  },
  overrides: [
    {
      files: '*.mdx',
      extends: 'plugin:mdx/recommended',
    },
  ],
  plugins: ['prettier'],
  rules: {
    'prettier/prettier': [
      'error',
      {
        singleQuote: true,
        semi: false,
        printWidth: 100,
        jsxSingleQuote: true,
        endOfLine: 'auto',
        indent: 2,
        tabWidth: 2,
      },
    ],
    'no-undef': 'off',
    'no-unused-vars': 'off',
    '@typescript-eslint/no-explicit-any': ['off'],
    'react/react-in-jsx-scope': 'off',
    'react/prop-types': 0,
  },
  globals: {
    MockDatabase: 'writable',
  },
}
