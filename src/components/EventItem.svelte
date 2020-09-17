<script>
  import {
    getShortMonthName,
    getStartToEndDateString,
    getStartToEndTimeString,
    isOngoing,
    isToday,
  } from "../modules/DateTimeUtils.mjs";
  export let event;
  const getDates = (event) => {
      if (!event.biws__datetime_meta) {
        return {};
      }

      const startDate = event.biws__datetime_meta.datetime__start_date;
      const endDate = event.biws__datetime_meta.datetime__end_date;

      return {
        start: startDate ? new Date(Date.parse(startDate)) : undefined,
        end: endDate ? new Date(Date.parse(endDate)) : undefined,
        today: new Date(Date.parse(new Date().toISOString().substr(0, 10))),
      };
    },
    getTimes = (event) => {
      if (!event.biws__datetime_meta) {
        return {};
      }

      const today = new Date();
      const startTime = event.biws__datetime_meta.datetime__start_time;
      const endTime = event.biws__datetime_meta.datetime__end_time;
      const nowTime = `${today.getHours()}:${today.getMinutes()}`;

      return {
        start: startTime,
        end: endTime,
        now: nowTime,
      };
    },
    getLocations = (location) => {
      let output = [];
      output.push(location.name);
      output.push(location.building);
      output.push(
        [location.street, location.street_nr].filter(Boolean).join(" ")
      );
      output.push([location.zip, location.location].filter(Boolean).join(" "));
      return output.filter(Boolean).join(", $").split("$");
    },
    lazyMail = async (node, data) => {
      setTimeout(() => {
        node.setAttribute("href", data.href);
      }, 2500);
    };

  $: dates = getDates(event);
  $: times = getTimes(event);
  $: ongoing =
    dates &&
    times &&
    isOngoing(
      dates.today,
      dates.start,
      dates.end,
      times.now,
      times.start,
      times.end
    );
  $: today = dates && isToday(dates.today, dates.start, dates.end);
</script>

<style>
  :root {
    --taki-border-radius: 3px;
    --taki-red: #db3b0f;
    --taki-grey0: #fafafa;
    --taki-grey1: #eee;
    --taki-grey2: #e0e0e0;
  }

  .divider {
    height: 1px;
    background-color: var(--taki-grey2);
  }

  .event-item {
    word-break: break-word;
    border-radius: var(--taki-border-radius);
    margin: 0.5rem 0 1rem 0;
    -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
  }

  .event-item > * {
    padding: 1rem 4rem 2rem;
  }

  .spacer {
    width: 12rem;
    margin-left: -4rem;
    margin-right: 2rem;
    flex-shrink: 0;
  }

  .date_container {
    background-color: var(--taki-red);
    border-radius: 0 var(--taki-border-radius) var(--taki-border-radius) 0;
    -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);

    padding: 1.5rem;
    color: #fff;
    text-transform: uppercase;
    text-align: center;

    font-size: 2.5rem;
    line-height: 2.5rem;
  }

  .date_planning {
    font-size: 1.2rem;
  }

  .date_day {
    font-weight: bold;
  }

  .content {
    padding-bottom: 1rem;
  }

  .title {
    margin-top: 4rem;
    font-weight: 300;
    color: rgba(0, 0, 0, 0.8);
  }

  .info_container {
    flex-grow: 1;
  }

  .details_container {
    margin: 2rem 0;
  }

  .details_container > div {
    display: flex;
    margin-bottom: 1rem;
  }

  .details_container > div > * {
    padding-top: 0.4rem;
    display: block;
  }

  .details_container p {
    margin-bottom: 1rem;
  }

  .details_container p:last-of-type {
    margin-bottom: 0;
  }

  .details_container p span {
    display: inline-block;
    padding-right: 0.5rem;
  }

  .details_container svg {
    flex-shrink: 0;
    padding-right: 1rem;
  }

  .tags {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
  }

  .tag {
    color: #fff;
    font-family: "Inter var", -apple-system, BlinkMacSystemFont,
      "Helvetica Neue", Helvetica, sans-serif;
    font-size: 0.7em;
    margin-left: 0.5em;
    height: 2.3em;
    padding: .5em 1.2em;
    border-radius: 1.15em;
    -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
  }

  .footer {
    background-color: var(--taki-grey0);
    border-top: 1px solid var(--taki-grey1);
    border-radius: 0 0 var(--taki-border-radius) var(--taki-border-radius);
  }

  .contacts_container {
    display: block;
  }

  .contact_container {
    margin: 1rem;
    flex-shrink: 0;
    flex-grow: 1;
  }

  .contact_container > * {
    display: block;
  }

  .contact {
    font-weight: bold;
  }

  @media (min-width: 900px) {
    .event-item > * {
      display: flex;
    }

    .date_container {
      margin-bottom: 0;
    }

    .date_container > * {
      display: block;
    }

    .date_day {
      font-size: 5rem;
      line-height: 5rem;
    }

    .contacts_container {
      display: flex;
      flex-wrap: wrap;
      width: 100%;
    }

    .info_container {
      margin-right: 10rem;
    }

    .contacts_container {
      margin: 0 9rem;
    }
  }
</style>

<div class="event-item">
  <div class="content">
    <div class="spacer">
      <div class="date_container">
        {#if dates && dates.start instanceof Date && !today && !ongoing}
          <span class="date_day">{dates.start.getDate()}</span>
          <span class="date_month">
            {getShortMonthName(dates.start.getMonth())}
          </span>
        {:else if ongoing}
          <span class="date_planning">Laufend</span>
        {:else if today}
          <span class="date_planning">Heute</span>
        {:else}<span class="date_planning">In Planung</span>{/if}
      </div>
    </div>
    <div class="info_container">
      <h3 class="title">
        {@html event.title}
      </h3>
      {#if event.content}
        <div class="description">
          {@html event.content}
        </div>
      {/if}
      <div class="divider" />
      <div class="details_container">
        {#if dates && dates.start}
          <div class="details_item">
            <svg
              id="{event.slug}-date-icon"
              aria-labelledby="{event.slug}-date-title {event.slug}-date-desc"
              style="width:3rem;height:2.5rem"
              viewBox="0 0 24 24">
              <title id="{event.slug}-date-title">Start- und Enddatum</title>
              <desc id="{event.slug}-date-desc">Kalender-Icon</desc>
              <path
                fill="#888"
                d="M9,10H7V12H9V10M13,10H11V12H13V10M17,10H15V12H17V10M19,3H18V1H16V3H8V1H6V3H5C3.89,3
                3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0
                19,3M19,19H5V8H19V19Z" />
            </svg>
            <p>{getStartToEndDateString(dates.start, dates.end)}</p>
          </div>
        {/if}
        {#if times && times.start}
          <div class="details_item">
            <svg
              id="{event.slug}-time-icon"
              aria-labelledby="{event.slug}-time-title {event.slug}-time-desc"
              style="width:3rem;height:2.5rem"
              viewBox="0 0 24 24">
              <title id="{event.slug}-time-title">Start- und Endzeit</title>
              <desc id="{event.slug}-time-desc">Uhr-Icon</desc>
              <path
                fill="#888"
                d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0
                0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22
                2,17.5 2,12A10,10 0 0,1
                12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z" />
            </svg>
            <p>{getStartToEndTimeString(times.start, times.end)}</p>
          </div>
        {/if}
        {#if event.biws__location_tax}
          <div class="details_item">
            <svg
              id="{event.slug}-location-icon"
              aria-labelledby="{event.slug}-location-title {event.slug}-location-desc"
              style="width:3rem;height:2.5rem"
              viewBox="0 0 24 24">
              <title id="{event.slug}-location-title">
                Ort der Veranstaltung
              </title>
              <desc id="{event.slug}-location-desc">
                Kartenmarkierungs-Icon
              </desc>
              <path
                fill="#888"
                d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1
              14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22
              12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" />
            </svg>
            <div>
              {#each event.biws__location_tax as location}
                <p>
                  {#each getLocations(location) as elem}
                    <span>{elem}</span>
                  {/each}
                </p>
              {/each}
            </div>
          </div>
        {/if}
      </div>
      {#if event.biws__tags_tax}
        <div class="tags">
          {#each event.biws__tags_tax as tag}
            <span
              class="tag {tag.slug}"
              style="background-color:{tag.color}">{tag.name}</span>
          {/each}
        </div>
      {/if}
    </div>
  </div>
  <div class="footer">
    {#if event.biws__contact_tax}
      <div class="contacts_container">
        {#each event.biws__contact_tax as contact}
          <div class="contact_container">
            {#if contact.name}<span class="contact">{contact.name}</span>{/if}
            {#if contact.phone}<span class="tel">{contact.phone}</span>{/if}
            {#if contact.email}
              <a
                class="highlight-text"
                href="mail2"
                use:lazyMail={{ href: 'mailto:' + contact.email }}>{contact.email}</a>
            {/if}
          </div>
        {/each}
      </div>
    {/if}
  </div>
</div>
