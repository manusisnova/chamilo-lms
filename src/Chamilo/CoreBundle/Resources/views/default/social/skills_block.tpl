<div class="panel-group" id="skill-block" role="tablist"
     aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse"
                   data-parent="#skill-block" href="#skillList"
                   aria-expanded="true" aria-controls="skillList">
                    {{ "Skills" | trans }}
                </a>

                <div class="btn-group pull-right">
                    <a class="btn btn-xs btn-default dropdown-toggle"
                       data-toggle="dropdown" href="#">
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        {% if show_skills_report_link %}
                            <li>
                                <a href="{{ _p.web_main ~ 'social/my_skills_report.php' }}"> {{ 'SkillsReport'|trans }}</a>
                            </li>
                        {% endif %}

                        <li>
                            <a href="{{ _p.web_main ~ 'social/skills_wheel.php' }}"> {{ 'SkillsWheel'|trans }}</a>
                        </li>
                        <li>
                            <a href="{{ _p.web_main ~ 'social/skills_ranking.php' }}"> {{ 'YourSkillRankingX'|trans|format(ranking) }}</a>
                        </li>
                    </ul>
                </div>
            </h4>
        </div>
        <div id="skillList" class="panel-collapse collapse in" role="tabpanel"
             aria-labelledby="headingOne">
            <div class="panel-body">
                {% if skills %}
                    <figure class="list-badges">
                        {% for skill in skills %}
                            <figure class="thumbnail">
                                <a href="{{ skill.issue_url }}" target="_blank">
                                    <img title="{{ skill.name }}"
                                         class="img-responsive"
                                         src="{{ skill.icon }}" width="64"
                                         height="64" alt="{{ skill.name }}">
                                </a>
                                <figcaption class="caption text-center">
                                    <a href="{{ skill.issue_url }}"
                                       target="_blank">
                                        {{ skill.name }}
                                        {% if skill.source_name %}
                                            <br>
                                            <small>{{ skill.source_name }}</small>
                                        {% endif %}
                                    </a>
                                </figcaption>
                            </figure>
                        {% endfor %}
                    </figure>
                {% else %}
                    <p>{{ 'WithoutAchievedSkills'|trans }}</p>
                    <p>
                        <a href="{{ _p.web_main ~ 'social/skills_wheel.php' }}">{{ 'SkillsWheel'|trans }}</a>
                    </p>
                {% endif %}
            </div>
        </div>
    </div>
</div>