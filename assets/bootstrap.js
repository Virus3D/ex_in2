import { startStimulusApp } from '@symfony/stimulus-bundle';
import CommentSelectController from './controllers/comment_select_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
app.register('comment-select', CommentSelectController);
