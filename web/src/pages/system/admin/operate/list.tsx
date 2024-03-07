import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Link, Tag, Tooltip, Select, DateRangePicker } from 'tdesign-react/esm'
import { PageTable, MediaText, FilterItem, useSelect, ShowLinkModal } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'username',
        title: translate('system.operate.fields.user'),
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              <MediaText.Avatar image={row.avatar}>{row.nickname[0]}</MediaText.Avatar>
              <MediaText.Title>{row.nickname}</MediaText.Title>
              <MediaText.Desc>{row.username}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'request_method',
        title: translate('system.operate.fields.request'),
        minWidth: 210,
        cell: ({ row }) => {
          return (
            <div className='flex flex-col gap-2'>
              <div>
                <Tooltip content={row.request_url}>
                  <Link>{row.route_name}</Link>
                </Tooltip>
              </div>
              <div className='flex gap-2'>
                <Tag theme='primary' variant='outline'>
                  {row.request_method}
                </Tag>
                <Tag theme='success' variant='outline'>
                  {row.request_time}
                </Tag>
              </div>
            </div>
          )
        },
      },
      {
        colKey: 'client_ip',
        title: translate('system.operate.fields.client'),
        minWidth: 200,
        cell: ({ row }) => {
          return (
            <div className='flex flex-col gap-2'>
              <div>{row.client_ip}</div>

              <div className='flex gap-2'>
                <Tag theme='primary' variant='outline'>
                  {row.client_device}
                </Tag>
              </div>
            </div>
          )
        },
      },
      {
        colKey: 'time',
        title: translate('system.operate.fields.requestTime'),
        minWidth: 200,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 160,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <ShowLinkModal component={() => import('./show')} rowId={row.id} />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  const { options, onSearch, queryResult } = useSelect({
    resource: 'system.user',
    optionLabel: 'nickname',
    optionValue: 'id',
  })

  return (
    <PageTable
      columns={columns}
      table={{
        rowKey: 'id',
      }}
      title={translate('system.operate.name')}
      filterRender={() => {
        return (
          <>
            <FilterItem name='user'>
              <Select
                filterable
                loading={queryResult.isLoading}
                onSearch={onSearch}
                options={options}
                placeholder={translate('system.operate.filters.userPlaceholder')}
                clearable
              />
            </FilterItem>
            <FilterItem name='method'>
              <Select
                placeholder={translate('system.operate.filters.method.placeholder')}
                clearable
              >
                <Select.Option value='post'>
                  {translate('system.operate.filters.method.post')}
                </Select.Option>
                <Select.Option value='put'>
                  {translate('system.operate.filters.method.put')}
                </Select.Option>
                <Select.Option value='patch'>
                  {translate('system.operate.filters.method.patch')}
                </Select.Option>
                <Select.Option value='delete'>
                  {translate('system.operate.filters.method.delete')}
                </Select.Option>
              </Select>
            </FilterItem>
            <FilterItem name='date'>
              <DateRangePicker enableTimePicker clearable />
            </FilterItem>
          </>
        )
      }}
    />
  )
}

export default List
