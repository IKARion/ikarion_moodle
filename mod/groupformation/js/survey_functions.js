/**
 * moodle-mod_groupformation JavaScript for the questionaaire displaying and assistance
 * https://github.com/moodlepeers/moodle-mod_groupformation
 *
 *
 * @author Eduard Gallwas, Johannes Konert, René Röpke, Neora Wester, Ahmed Zukic
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(['jquery', 'jqueryui'], function ($, jqueryui) {
    $(document).ready(function () {

        $("#dialog").dialog({
            bgiframe: true,
            autoOpen: false,
            height: 300,
            modal: true,
            buttons: {
                OK: function() {
                    $("#dialog > form").submit();
                    $(this).dialog('close');
                },
                Abbrechen: function() {
                    $(this).dialog('close');
                }
            }
        });
        $('#benutzerDel').click(function() {
            $('#dialog').dialog('open');
        });

        $('#invisible_topics_inputs').hide();
        $(".maxgroupsizenotreached_header").click(function () {
        });
        $(".groupsbuilt_header").click(function () {
        });

        $('.freetext-checkbox').click(function() {
            console.log($(this).parent().parent().parent().find($(".freetext-textarea")));
            name = $(this).attr("name");
            textarea = $(this).parent().parent().parent().find($(".freetext-textarea"));
            $(textarea).prop('disabled', function(i, v) { return !v; });
        });

        // Get the widths of all navigation li's.
        var menuWidths = $('#accordion li').map(function (i) {
            return $(this).outerWidth();
        });

        // Shrink all widths to 50.
        $("#accordion li.accord_li").each(function () {
            $(this).width(50);
        });

        var activeItem = $();

        // Hover event.
        function accordion_rollout() {
            $(activeItem).animate({width: "50px"}, {duration: 300, queue: false});
            var a_width = menuWidths.get($(this).index()) + 1;
            $(this).animate({width: a_width}, {duration: 300, queue: false});
            activeItem = this;
        }

        // Mouse leave event.
        function accordion_rollin() {
            $(activeItem).animate({width: "50px"}, {duration: 300, queue: false});
        }

        $("#accordion li.accord_li").hover(accordion_rollout, accordion_rollin);
        $("#accordion li.accord_li a").focus(function () {
            accordion_rollout.call($(this).parent());
        }, function () {
            accordion_rollin.call($(this).parent());
        });

        // If the questionnaire still available but the answers already submitted.
        if ($('#commited_view').length) {
            $('table.responsive-table').find('input, select').prop('disabled', true);
        } else {
            // Clickable wraper for input radios.
            $(".select-area").click(function () {
                var name = $(this).find('input:radio').attr('name');
                $('input[name="' + name + '"]').parent().removeClass('selected_label');
                $('input[name="' + name + '"]').parent().parent().removeClass('noAnswer');
                $(this).addClass('selected_label');
                $(this).find('input:radio').prop('checked', true);
            });

            // Drag & Drop the topics/objects to sort them.
            $('.sortable_topics').sortable({
                axis: 'y',
                stop: function (event, ui) {
                    $('#invisible_topics_inputs').find('input').remove();
                    createTopicInputs();
                }
            });

            // Create hidden Inputs of Topics to write their order to db.
            function createTopicInputs() {
                var sortedIDs = $(".sortable_topics").sortable("toArray");
                $.each(sortedIDs, function (index, value) {
                    $('<input type="text" name="' + value + '" value="' + (sortedIDs.length - index ) + '">').appendTo('#invisible_topics_inputs');
                });

            }

            createTopicInputs();

            // Write to hidden inputs to mark range-inputs as valid when they get clicked.
            $('.gf_range_inputs').click(function () {
                $('input[name="' + $(this).prop('name') + '_valid"]').val(1);
                $('#text' + $(this).prop('name')).text($(this).prop('value'));
            });

            // Get map of grade/points values.
            var values = $('#grade1 option').map(function (i) {
                return $(this).val();
            });

            values.get();

            // Manipulate grades on change.
            $('#grade1').change(function () {
                var grade1 = $(this).val();
                $('#grade3 option').prop('selected', false)
                    .filter('[value="' + (parseInt(grade1) + 1) + '"]')
                    .prop('selected', true);

                $('#grade3 option').each(function () {
                    if ($(this).val() < grade1) {
                        $(this).attr('disabled', true);
                    }
                });
            });
        }
    });
});