/** @namespace */
var ILD = ILD || {};

/**
 * Interactions counter
 * @type {Array}
 */
ILD.interactions = [];

/**
 * Answered Layer
 * @type {Array}
 */
ILD.answered = [];

/**
 * SubContentIds - avoid duplicated answered statement
 * @type {Array}
 */
ILD.subIds = [];

/**
 * Stores QuestionSet PassPercentage
 * @type {Array}
 */
ILD.questionSetPassPercentage = [];

/**
 * Internal H5P function listening for xAPI answered events and stores scores.
 *
 * @param {H5P.XAPIEvent} event
 */
ILD.xAPIAnsweredListener = function (event) {
    console.log(event);
    var contentId = event.getVerifiedStatementValue(['object', 'definition', 'extensions', 'http://h5p.org/x-api/h5p-local-content-id']);

    if (typeof ILD.questionSetPassPercentage[contentId] === 'undefined' && event.getVerb() === 'answered') {
        var score = event.getScore();
        var maxScore = event.getMaxScore();
        var subContentId = event.data.statement.object.id;

        if ((ILD.subIds.indexOf(subContentId) == -1) && (score == maxScore)) {
            if (typeof ILD.answered[contentId] === 'undefined') {
                ILD.answered[contentId] = 1;
            }

            if (typeof ILD.interactions[contentId] === 'undefined') {
                ILD.interactions[contentId] = 1;
            }

            var answered = ILD.answered[contentId];
            var interactions = ILD.interactions[contentId];
            var percentage = (answered / interactions) * 100;

            ILD.setResult(contentId, percentage, 100);
            ILD.answered[contentId] += 1;
            ILD.subIds.push(subContentId);
        }
    }

    // Check if interactive video has no interaction - set complete after finished watching
    if ((typeof ILD.interactions[contentId] === 'undefined' || ILD.interactions[contentId] == 0) &&
        typeof ILD.questionSetPassPercentage[contentId] === 'undefined' &&
        event.getVerb() === 'completed') {
        ILD.setResult(contentId, 100, 100);
    }

    // Check if QuestionSet is completed an percentage is set.
    if (typeof ILD.questionSetPassPercentage[contentId] !== 'undefined' && event.getVerb() === 'completed') {
        var score = event.getScore();
        var maxScore = event.getMaxScore();
        var percentage = (score / maxScore) * 100;
        var passPercentage = ILD.questionSetPassPercentage[contentId];

        if (percentage >= passPercentage) {
            ILD.setResult(contentId, 100, 100);
        }
    }
};

/**
 * Post answered results for user and set progress.
 *
 * @param {number} contentId
 *   Identifies the content
 * @param {number} score
 *   Achieved score/points
 * @param {number} maxScore
 *   The maximum score/points that can be achieved
 */
ILD.setResult = function (contentId, score, maxScore) {
    $.post(H5PIntegration.ajax.setFinished, {
        contentId: contentId,
        score: score,
        maxScore: maxScore
    }, function (data) {
        var div_id = String('oc-progress-' + data.sectionId);
        var text_div_id = String('oc-progress-text-' + data.sectionId);
        var percentage = Math.round(data.percentage);
        percentage = String(percentage + '%');

        $('#' + div_id, window.parent.document).css('width', percentage);
        $('#' + text_div_id, window.parent.document).html(percentage);
    }, 'json');
};

/**
 * Count interactions layers from interactive video element.
 *
 * @param contentId
 * @param content
 */
ILD.getVideoInteractions = function (contentId, content) {
    var interactions = content.interactiveVideo.assets.interactions;
    var notAllowedInteractions = ['H5P.Text', 'H5P.Table', 'H5P.Link', 'H5P.Image', 'H5P.GoToQuestion', 'H5P.Summery', 'H5P.Nil', 'H5P.IVHotspot'];
    var interactionsCounter = 0;

    if (typeof interactions === 'object') {
        $.each(interactions, function (i) {
            var library = interactions[i].action.library;
            var foundItem = false;

            $.each(notAllowedInteractions, function (j) {
                if (library.indexOf(notAllowedInteractions[j]) > -1) {
                    foundItem = true;
                }
            });

            if (!foundItem) interactionsCounter++;
        });

        ILD.interactions[contentId] = interactionsCounter;
    }
};

/**
 * Count interactions layers from SingleChoice element.
 *
 * @param contentId
 * @param content
 */
ILD.getSingleChoiceInteractions = function (contentId, content) {
    var interactions = content.choices.length;

    console.log(interactions);

    ILD.interactions[contentId] = interactions;
};

/**
 *
 * @param contentId
 * @param content
 */
ILD.getQuestionSetPercentage = function (contentId, content) {
    ILD.questionSetPassPercentage[contentId] = content.passPercentage;
}

/**
 * Check if library is InteractiveVideo or QuestionSet.
 */
ILD.checkLibrary = function () {
    //var contentId = $('iframe[class="h5p-iframe h5p-initialized"]').data('content-id');
    var contentId = $('.h5p-content.h5p-initialized').data('content-id');

    if (typeof contentId !== 'undefined') {
        var contentData = H5PIntegration.contents['cid-' + contentId];
        var content = JSON.parse(contentData.jsonContent);
        var library = contentData.library;

        console.log(content);
        console.log(library);

        if (library.indexOf('H5P.InteractiveVideo') > -1) {
            ILD.getVideoInteractions(contentId, content);
        } else if (library.indexOf('H5P.QuestionSet') > -1) {
            ILD.getQuestionSetPercentage(contentId, content);
        } else if (library.indexOf('H5P.SingleChoiceSet') > -1) {
            ILD.getSingleChoiceInteractions(contentId, content);
        }
    }
}

//$(window).load(function () {
$(window).on('load', function () {
    ILD.checkLibrary();
    H5P.externalDispatcher.on('xAPI', ILD.xAPIAnsweredListener);
});