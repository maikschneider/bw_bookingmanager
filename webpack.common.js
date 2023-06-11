const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  plugins: [new MiniCssExtractPlugin({
    filename: 'Resources/Public/Css/backend/BackendCalendar.css',
  })],
  entry: {
    BackendModuleCalendar: './Resources/Private/TypeScript/BackendModuleCalendar.ts',
    BackendModalCalendar: './Resources/Private/TypeScript/BackendModalCalendar.ts',
    BackendCalendarContextMenuActions: './Resources/Private/TypeScript/BackendCalendarContextMenuActions.ts',
    BackendEntryListConfirmation: './Resources/Private/TypeScript/BackendEntryListConfirmation.ts',
    BackendEntryListButtons: './Resources/Private/TypeScript/BackendEntryListButtons.ts',
    BackendFormElementIcsSecret: './Resources/Private/TypeScript/BackendFormElementIcsSecret.ts',
    BackendFormElementSelectTimeslot: './Resources/Private/TypeScript/BackendFormElementSelectTimeslot.ts'
  },
  output: {
    filename: 'Resources/Public/JavaScript/[name].js',
    path: path.resolve(__dirname, '.'),
    libraryTarget: 'amd',
    library: "TYPO3/CMS/BwBookingmanager/[name]"
  },
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        use: 'ts-loader'
      },
      {
        test: /\.css$/i,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
      {
        test: /\.s[ac]ss$/i,
        use: [
          MiniCssExtractPlugin.loader,
          "css-loader",
          "sass-loader",
        ],
      },
    ],
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js'],
    alias: {
      'TYPO3/CMS/Backend': path.resolve(__dirname, 'typo3_src/TypeScript/backend/Resources/Public/TypeScript'),
      'TYPO3/CMS/Core': path.resolve(__dirname, 'typo3_src/TypeScript/core/Resources/Public/TypeScript'),
      'TYPO3/CMS/BwBookingmanager': path.resolve(__dirname, 'Resources/Private/TypeScript')
    }
  },
  externals: {
    'TYPO3/CMS/Backend/Modal': 'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Backend/Icons': 'TYPO3/CMS/Backend/Icons',
    'TYPO3/CMS/Backend/Notification': 'TYPO3/CMS/Backend/Notification',
    'jquery': 'jquery'
  }
};
